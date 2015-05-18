@extends('app')

@section('container')

	<div class="cover-about bottom40">
		<div class="container">

			<h2 class="bottom40">Documentation</h2>

		</div>
	</div>

	<div class="container">

		<div class="row">
			<div class="col-lg-12">
				<h2>Push Event</h2>
				<span class="label label-danger">AUTH</span> <span class="label label-info">POST</span> :: /api/push
				<p>This endpoint pushes an event to all listening clients on a single site.</p>

				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Parameter</th>
							<th>Required</th>
							<th>Requirements</th>
							<th>Description</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>private</td>
							<td><span class="label label-success">yes</span></td>
							<td>It must be a valid private key from one of your sites, exactly 60 characters.</td>
							<td>The private key of a site you manage.</td>
						</tr>
						<tr>
							<td>event</td>
							<td><span class="label label-success">yes</span></td>
							<td>Must be a string with no spaces.</td>
							<td>The event name, EG. "blog_post".</td>
						</tr>
						<tr>
							<td>channel</td>
							<td><span class="label label-danger">no</span></td>
							<td>Must be a valid channel name, defaults to 'public' if none given.</td>
							<td>a name of a channel your site has.</td>
						</tr>
						<tr>
							<td>payload</td>
							<td><span class="label label-danger">no</span></td>
							<td>Must be a JSON string.</td>
							<td>The set of data you may want the client to use.</td>
						</tr>
					</tbody>
				</table>

				<div class="row">
					<div class="col-lg-6">
						<h5>Request</h5>
<pre><code class="json">POST /api/push HTTP/1.1
Host: localhost
Cache-Control: no-cache
Content-Type: multipart/form-data;
{
    "private": "qYna0HGtVAUyCePv67RwCLh6",
    "event": "kittens",
    "channel": "auth",
    "payload": {
        "foo" : "bar"
    }
}
</code></pre>
					</div>
					<div class="col-lg-6">
						<h5>Response</h5>
<pre><code class="javascript">{
    "status": "success",
    "message": "Event pushed successfully.",
    "event": "kittens",
    "channel": "auth",
    "site": "dfl.mn",
    "timestamp": {
        "date": "2015-05-15 22:40:29.000000",
        "timezone_type": 3,
        "timezone": "UTC"
    },
    "payload": {
        "foo": "bar"
    }
}
</code></pre>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<hr>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<h3>Site Usage</h3>

				<div class="about_font">
					<p>Pushman runs on multiple sites so from your dashboard you need to first build a new site.</p>

					<h4>Sites</h4>
					<p>Each site then has multiple channels you can push events through. By default you won't always need to use channels, if you never touch channels Pushman will default to the public channel.</p>

					<h4>Channels</h4>
					<p>Multiple channels allow for private events and payloads to be pushed to users. Say for example you want to alert admins when a blog post was created, if you always used the 'public' channel, then every user could use their public token to subscribe to that event and listen in.</p>

					<p>In this case, you would create an 'auth' channel and push the 'new_blog' event down that channel which would stop guests listening in.</p>

					<p>You can then have another 'admin' channel where only admin events are pushed. Everyone has access to the public channels, only certain users have access to the other private channels.</p>

					<h6>Max Connections</h6>
					<p>This is just another added measure of security, say you have a team of 6 people to administer your site. You should also set the max connections of your 'admin' channel to 6. Therefore even if someone did get the key, they wouldn't also have a spot on the connection list ready for them, and if a legitimate user cannot connect, you'll know someone grabbed a private token they shouldn't have and reset the token.</p>

					<h6>Auto Refreshing Tokens</h6>
					<p>Say for example you ban someone on your site so they can no longer access your 'auth' channel. If they copied the token for the 'auth' channel, then you'd have to reset your token every single time you ban a user.</p>
					<p>Auto Refreshing tokens are reset every 60 minutes, so your server pings Pushman servers in advanced, grabs the appropriate token for this 60 minute period and passes it onto the client.</p>
					<p>If you ban a user, or demote them from being an admin, then they would only keep access to the channel for 60 minutes at most.</p>

				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<hr>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<h3>Javascript Setup</h3>

				<h4>Example Client</h4>
				<table class="table">
					<tbody>
						<tr>
							<th>Site</th>
							<td>http://test.com</td>
						</tr>
						<tr>
							<th>'Public' Channel Token</th>
							<td><code>CXsujMXhMbPlirEBKaFP</code></td>
						</tr>
						<tr>
							<th>'Auth' Channel Token</th>
							<td><code>tKGrLcKIaPzccjHIioXc</code></td>
						</tr>
					</tbody>
				</table>
<pre><code><?php echo(e('<script src="http://autobahn.s3.amazonaws.com/js/autobahn.min.js"></script>')); ?></code></pre>
<pre><code class="javascript"><?php echo(e('var conn = new ab.Session(\'ws://pushman.dfl.mn:8080?token=CXsujMXhMbPlirEBKaFP\',
    function() {
        conn.subscribe(\'auth(tKGrLcKIaPzccjHIioXc)|kittens\', function(topic, data) {
            // Subscribes to the `kittens` event on the `auth` channel.
            console.log(data);
        });
        conn.subscribe(\'kittens_are_cute\', function(topic, data) {
            // Subscribes to the `kittens_are_cute` event on the `public` channel.
            console.log(data);
        });
    },
    function() {
        console.warn(\'WebSocket connection closed\');
    },
    {\'skipSubprotocolCheck\': true}
);')); ?>
</code></pre>
			</div>
		</div>
	</div>

@endsection

@section('javascript')
@parent
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
@endsection