<?php

class Licence {

	// for each product ensure there's a redis key
	// containing the number of licences available
	// keys regenerate if people leave their tabs open for long than a day
	// or the db state gets in mess because the socket service crashes, etc
	public static function refresh_licencing_info() {
		$redis = new Redis();
		if ($redis->connect(Config::get('REDIS_HOST'), Config::get('REDIS_PORT'))) {
			$redis->setOption(Redis::OPT_PREFIX, Config::get('REDIS_PREFIX'));

			$database = DatabaseFactory::getFactory()->getConnection();
			$query = $database->prepare("
				select md5(s.referenceId) refId, t.concurrency from subscriptions s
				inner join product p on p.id = s.product_id
				inner join app_tiers t on (p.entity_id = t.id and p.entity = 'app_tiers' and p.active = 1)

				union

				select md5(s.referenceId) refId, t.concurrency from subscriptions s
				inner join product p on p.id = s.product_id
				left outer join bundle_apps b on (p.entity_id = b.bundle and p.entity = 'bundle' and p.active = 1)
				inner join app_tiers t on t.id = b.`app_tier`
			");
			// the old way only calculated on api products
			// select s.referenceId, t.concurrency
			// from subscriptions s
			// inner join product p on p.id = s.product_id
			// inner join app_tiers t on (p.entity_id = t.id and p.entity = 'app_tiers')
			// where p.product_id like 'api-%'
			// and s.status = 'active'
			$query->execute();
			$touched = false;
			foreach ($query->fetchAll() as $row) {
				$licenses = intval($row->concurrency,10);
				if (!$redis->exists($row->refId)) {
					$redis->incrBy($row->refId, $licenses);
					$redis->expire($row->refId, 86400); // expire this key after 1 day
					$touched = true;
				}
			}
			if ($touched) {
				$redis->save(); // sync
			}
			$redis->close();
		}
	}

	// return the number of places in total for a subscription
	public static function total_seats($hash) {
		$database = DatabaseFactory::getFactory()->getConnection();
		// this will either match on an app_tier or a bundle, but not both, one half of the union will always be empty
		// the last union is a fallback so that the default total seats is 1, in case the subscription isn't found
		$query = $database->prepare("
			SELECT t.concurrency from subscriptions s
			inner join product p on p.id = s.product_id
			inner join app_tiers t on (p.entity_id = t.id and p.entity = 'app_tiers' and p.active = 1)
			where md5(s.referenceId) = :hash

			union

			SELECT t.concurrency from subscriptions s
			inner join product p on p.id = s.product_id
			left outer join bundle_apps b on (p.entity_id = b.bundle and p.entity = 'bundle' and p.active = 1)
			inner join app_tiers t on t.id = b.`app_tier`
			where md5(s.referenceId) = :hash

			union

			SELECT 1
		");
		$query->execute(array(
			":hash" => $hash
		));
		return (int) $query->fetchColumn();
	}


	// return the value of the places remaining for a given md5(subscription.referenceId)
	public static function seats_remaining($hash) {
		$redis = new Redis();
		$places = -1;
		if ($redis->connect(Config::get('REDIS_HOST'), Config::get('REDIS_PORT'))) {
			$redis->setOption(Redis::OPT_PREFIX, Config::get('REDIS_PREFIX'));
			$places = intval($redis->get($hash), 10);
			$redis->close();
		}
		return (int) $places;
	}
}