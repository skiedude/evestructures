
@extends('layouts.app')

@section('content')
@include('layouts.errors');

<div class="container">
	<div class="blog-main">
	  <div class="panel panel-primary">
	    <div class="panel-heading"><h3 class="panel-title"><strong>What is EveStructures?</strong></h3></div>
	      <div class="panel-body">
					<p>EveStructures creates a consolidated view of all your in-game player owned stations. No longer do you need to login in dozens of characters to check fuel timers, service status or even to remember all the structures you have!</p>
					<p>Now, from one view you can see everything on the fly. Structures are grouped by the character that has access to view them, and shows the most pertinent information:
						<ul>
							<li>Name</li> 
							<li>System</li>
							<li>Fuel Time</li> 
						</ul>
					Clicking on the structures name will take you to a more in-depth view. There you can see: 
						<ul>
							<li>services with their status</li>
							<li>current state end/start time</li>
							<li>unanchor timer</li>  
							<li>and much more!</li>
						</ul>
					Check out the <a href="/demo">Demo view</a> to see how it works</p>
				</div>
	  </div>

	  <div class="panel panel-primary">
	    <div class="panel-heading"><h3 class="panel-title"><strong>Why do I have to register, not just sign in with SSO?</strong></h3></div>
	      <div class="panel-body">
					<p>The main benefit of EveStructures is the ability to view multiple characters in one place. Signing on through a single character means you are limited to that Characters scope. By making an account, we can now associate multiple characters to one user.</p>
					<p>Emails are used for password reset mails. You are more than welcome to use a spam, junk or burner email</p>
					<p>This also allows you to have one login for all your Station Managers in your Corporation/Alliance. <strong>It is not required for you to have your own account</strong>.</p>
				</div>
	  </div>

	  <div class="panel panel-primary">
	    <div class="panel-heading"><h3 class="panel-title"><strong>What permissions does EveStructures request?</strong></h3></div>
	      <div class="panel-body">
					<p>The scopes required to pull all the necessary info are:
						<ul>
							<li><a href="https://esi.tech.ccp.is/ui/#/Corporation/get_corporations_corporation_id_structures" target="_blank">esi-corporations.read_structures.v1</a>
								<ul style="list-style-type:none">
									<li>Get a list of corporation structures (cached for up to 3600 seconds)</li>
									<li>This requires the character to have the Station Manager role in game.</li>
								</ul>
							</li> 
							<li><a href="https://esi.tech.ccp.is/ui/#/Character/get_characters_character_id_roles" target="_blank">esi-characters.read_corporation_roles.v1</a>
								<ul style="list-style-type:none">
									<li>Returns a character's corporation roles (cached for up to 3600 seconds)</li>
									<li>Before requesting structures, we confirm the character has the Station Manager role in game, this reduces the number of errors we can cause requesting with an unauthorized character</li>
								</ul>
							</li>
							<li><a href="https://esi.tech.ccp.is/ui/#/Universe/get_universe_structures_structure_id" target="_blank">esi-universe.read_structures.v1</a>
								<ul style="list-style-type:none">
									<li>Returns information on requested structure, if you are on the ACL. Otherwise, returns "Forbidden" for all inputs. (cached for up to 3600 seconds)</li>
									<li>In order to get the stations name, we have to hit this ESI Endpoint with the structure_id on a character that has ACL access.</li>
								</ul>
							</li> 
						</ul>
						You can always see (and revoke) which Eve Websites/Apps have access to your information through ESI by visiting: <a href="https://community.eveonline.com/support/third-party-applications/" target="_blank">Third Party Applications</a>.
					</p>
					<p>As part of our Character deletion and Account deletion functions we delete all character data, structure data and  revoke our access through the API.</p>
				</div>
	  </div>

		<div class="panel panel-warning">
			<div class="panel-heading"><h3 class="panel-title text-center"><strong>Disclaimer</strong></h3></div>
				<div class="panel-body">
				<p>EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved worldwide. All other trademarks are the property of their respective owners. EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of CCP hf. All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the intellectual property relating to these trademarks are likewise the intellectual property of CCP hf. CCP hf. has granted permission to EveSkillboard to use EVE Online and all associated logos and designs for promotional and information purposes on its website but does not endorse, and is not in any way affiliated with, EveSkillboard. CCP is in no way responsible for the content on or functioning of this website, nor can it be liable for any damage arising from the use of this website. </p>
			</div>
		</div>
	</div> <!-- close blog-main -->
</div><!-- close container -->
@endsection
