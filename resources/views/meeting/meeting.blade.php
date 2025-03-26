<!doctype html>
<html>
  <head>
    <title>Reach Meet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
       html, body {
       	margin: 0;
       	height: 100vh;
       }
       #meet {
       	height: 100vh;
       }
    </style>
  </head>
  <body>
    <div id="meet"></div>
  </body>
  
  <script src='https://beta2.reach.boats/external_api.js'></script>
  
  <script>
  	const domain = 'beta2.reach.boats';
	const options = {
		roomName: '{{ $data["meeting_id"] }}/Reach Meet',
		userInfo: {
		    displayName: '{{ $data["member"]->members_fname }} {{ $data["member"]->members_lname }}',
		},
		parentNode: document.querySelector('#meet'),
		lang: 'en',
		configOverwrite: {
			startWithAudioMuted: true,
			prejoinConfig: {
				enabled: false
			}
		},
	};
	const api = new JitsiMeetExternalAPI(domain, options);
	
	<?php
	if($data["member"]->members_profile_picture != '') {
		$profilePic = asset('storage/' . $data["member"]->members_profile_picture);
		echo "api.executeCommand('avatarUrl', '$profilePic')";
	}
	?>
  </script>
</html>
