define({  "name": "CourseSuite API Documentation",  "version": "0.9.0",  "description": "Authenticate and Launch CourseSuite Apps using programmatic access",  "title": "CSAPI",  "url": "https://www.coursesuite.ninja",  "header": {    "title": "Introduction",    "content": "<p>The CourseSuite API lets you generate tokens for <em>launching</em> apps, and make modifications to the appearance of the apps. We are planning that a later revision of the API will later be revised with methods for querying the usage patterns of apps, applying custom templates and other functions.</p>\n<p>Some of the methods require a BEARER TOKEN header, whilst other methods require DIGEST AUTHENTICATION.</p>\n<p>Bearer Tokens are a header attached to the request. For example (curl):</p>\n<pre><code>curl_setopt($ch, CURLOPT_HTTPHEADER, array(&quot;Authorization&quot; =&gt; &quot;Bearer: $key&quot;));\n</code></pre>\n<p>Digest Authentication is an initial query which sends the username and password in a cipher specified by the server. E.g. (curl)</p>\n<pre><code>curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);\ncurl_setopt($ch, CURLOPT_USERPWD, $key . &quot;:&quot; . $secret);\n</code></pre>\n<p>All methods require HTTPS.</p>\n"  },  "footer": {    "title": "Example (PHP)",    "content": "<h2>PHP Example (using curl)</h2>\n<pre><code class=\"language-php\">&lt;?php\n\n/**\n * enter your apikey and secret as the username and password\n *\n */\n$host = &quot;https://www.coursesuite.ninja&quot;;\n$username = &quot;c055588e18df56f877f3c3ca73790ecd&quot;;\n$password = &quot;b6e8b4699&quot;;\n\n/**\n * call the info endpoint to determine which tools are available\n *\n */\n$ch = curl_init();\ncurl_setopt($ch, CURLOPT_URL, $host . &quot;/api/info/&quot;);\ncurl_setopt($ch, CURLOPT_HTTPHEADER, array(&quot;Authorization&quot; =&gt; &quot;Bearer: $username&quot;));\ncurl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);\ncurl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);\ncurl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);\ncurl_setopt($ch, CURLOPT_RETURNTRANSFER, true);\ncurl_setopt($ch, CURLOPT_TIMEOUT, 10);\n$resp = curl_exec($ch);\nif (curl_errno($ch)) {\n    die(curl_error($ch));\n}\n$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);\nif ($status_code != 200) {\n    die(&quot;Error $status_code $resp&quot;);\n}\ncurl_close($ch);\n$info_model = json_decode($resp);\n\n/**\n * generate a token for launch\n *\n */\n$ch = curl_init();\ncurl_setopt($ch, CURLOPT_URL, $host . &quot;/api/createToken/&quot;);\ncurl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);\ncurl_setopt($ch, CURLOPT_USERPWD, $username . &quot;:&quot; . $password);\ncurl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);\ncurl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);\ncurl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);\ncurl_setopt($ch, CURLOPT_RETURNTRANSFER, true);\ncurl_setopt($ch, CURLOPT_TIMEOUT, 10);\n$resp = curl_exec($ch);\nif (curl_errno($ch)) {\n    die(curl_error($ch));\n}\n$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);\nif ($status_code != 200) {\n    die(&quot;Error $status_code $resp&quot;);\n}\ncurl_close($ch);\n$token_model = json_decode($resp);\n\n/**\n * render a basic document that contains the links to the apps\n *\n */\n\n ?&gt;\n&lt;!doctype html&gt;\n&lt;html&gt;\n&lt;body&gt;\n&lt;?php\nforeach ($info_model as $index =&gt; $app) {\n\n    $url = str_replace('{token}', $token_model-&gt;token, $app-&gt;launch);\n\n    echo &quot;&lt;fieldset&gt;&lt;legend&gt;&quot; . $app-&gt;name . &quot;&lt;/legend&gt;&quot;;\n    echo &quot;&lt;figure&gt;&lt;img src='&quot; . $app-&gt;icon . &quot;' /&gt;&quot;;\n    echo &quot;&lt;figcaption&gt;&quot;;\n    echo &quot;&lt;a href='&quot; . $url . &quot;' target='&quot; . $app-&gt;app_key . &quot;'&gt;Launch&lt;/a&gt;&quot;;\n    echo &quot;&lt;/figcaption&gt;&quot;;\n    echo &quot;&lt;/fieldset&gt;&quot; . PHP_EOL;\n\n}\n?&gt;\n&lt;/body&gt;&lt;/html&gt;\n</code></pre>\n<h2>Getting Support</h2>\n<p>If you need any help related to the API, please <a href=\"https://help.coursesuite.ninja/open.php\">raise a ticket</a> on our helpdesk.</p>\n"  },  "order": [    "Names",    "Generate",    "Launch"  ],  "sampleUrl": false,  "defaultVersion": "0.0.0",  "apidoc": "0.3.0",  "generator": {    "name": "apidoc",    "time": "2018-04-11T03:13:25.417Z",    "url": "http://apidocjs.com",    "version": "0.17.6"  }});
