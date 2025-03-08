<?php
include('dbcon.php');

//Check whether the session variable SESS_MEMBER_ID is present or not
if (!isset($_SESSION['user_id']) || (trim($_SESSION['user_id']) == '')) {
	header("location: index.php");
	exit();
}
$session_id = $_SESSION['user_id'];

$query = "SELECT * FROM myaccount WHERE facebook_id='$session_id' ";
$user_query = mysqli_query($conn, $query);
$row = mysqli_fetch_array($user_query);
?>






<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href=".//style/style.css">
	<link rel="icon" type="image/x-icon" href="images/logo/fb.png">
	<title>Facebook</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			font-family: 'poppins', sans-serif;
			box-sizing: border-box;
		}

		:root {
			--body-color: #efefef;
			--nav-color: white;
			--bg-color: #fff;
		}

		.dark-theme {
			--body-color: #0a0a0a;
			--nav-color: #000;
			--bg-color: #000;
		}


		/* DARK BACKGROUND COLOR */
		body {
			background: var(--body-color);
			transform: background 0.3s;
		}

		nav {
			display: flex;
			align-items: center;
			justify-content: space-between;
			background: var(--nav-color);
			padding: 0px 5%;
			position: sticky;
			top: 0;
			z-index: 100;
		}

		.online::after {
			content: '';
			width: 7px;
			height: 7px;
			border: var(--bg-color);
			border-radius: 50%;
			background: #41db51;
			position: absolute;
			top: 0;
			right: 0;
		}

		.profile-intro {
			background: var(--bg-color);
			padding: 20px;
			margin-bottom: 20px;
			border-radius: 4px;
		}

		.settings-menu {
			position: absolute;
			width: 90%;
			max-width: 350px;
			background: var(--bg-color);
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
			border-radius: 4px;
			overflow: hidden;
			top: 108%;
			right: 1%;
			max-height: 0;
			transition: max-height 0.3s;
		}

		.write-post-container {
			width: 100%;
			background: var(--bg-color);
			border-radius: 6px;
			padding: 20px;
			color: #626262;
		}

		.pd-right button {
			background: var(--nav-color);
			border: 0;
			outline: 0;
			padding: 6px 10px;
			display: inline-flex;
			align-items: center;
			color: #fff;
			border-radius: 3px;
			margin-left: 10px;
			cursor: pointer;
		}

		.post-container {
			width: 100%;
			background: var(--bg-color);
			border-radius: 6px;
			padding: 20px;
			color-scheme: #626262;
			margin: 20px 0;
		}

		.settings-menu-height {
			max-height: 450px;
		}

		.user-profile a {
			font-size: 12px;
			color: #1876f2;
			text-decoration: none;
		}

		.profile-details {
			background: var(--bg-color);
			padding: 20px;
			border-radius: 4px;
			display: flex;
			align-items: flex-start;
			justify-content: space-between;
		}

		/* SETTING MENU */
		.settings-menu-inner {
			padding: 20px;
		}

		.settings-menu hr {
			border: 0;
			height: 1px;
			background: #9a9a9a;
			margin: 15px 0;
		}

		.settings-links {
			display: flex;
			align-items: center;
			margin: 15px 0;
		}

		.settings-links .settings-icon {
			width: 38px;
			margin-right: 10px;
			border-radius: 50%;
		}

		.settings-links a {
			display: flex;
			flex: 1;
			align-items: center;
			justify-content: space-between;
			text-decoration: none;
			color: #626262;
		}

		#dark-btn {
			position: absolute;
			top: 20px;
			right: 20px;
			background: #ccc;
			width: 45px;
			border-radius: 15px;
			padding: 2px 3px;
			cursor: pointer;
			display: flex;
			transition: padding-left 0.5s, background 0.5s;
		}

		#dark-btn span {
			width: 18px;
			height: 18px;
			background: #fff;
			border-radius: 50%;
			display: inline-block;
		}

		#dark-btn.dark-btn-on {
			padding-left: 23px;
			background: #0a0a0a;
		}


		.profile-info {
			display: flex;
			align-self: flex-start;
			justify-content: space-between;
			margin-top: 20px;
		}

		.info-col {
			flex-basis: 33%;
		}

		.post-col {
			flex-basis: 65%;
		}

		.profile-intro {
			background: var(--bg-color);
			padding: 20px;
			margin-bottom: 20px;
			border-radius: 4px;
		}

		.profile-intro h3 {
			font-weight: 600;
		}

		.intro-text {
			text-align: center;
			margin: 10px 0;
			font-size: 15px;
		}

		.intro-text img {
			width: 15px;
			margin-bottom: -3px;
		}

		.profile-intro hr {
			border: 0;
			height: 1px;
			background: #ccc;
			margin: 24px 0;
		}

		.profile-intro ul li {
			list-style: none;
			font-size: 15px;
			margin: 15px 0;
			display: flex;
			align-items: center;
		}

		.profile-intro ul li img {
			width: 26px;
			margin-right: 10px;
		}

		.title-box {
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.title-box a {
			text-decoration: none;
			color: #1876f2;
			font-size: 14px;
		}

		.photo-box {
			display: grid;
			grid-template-columns: repeat(3, auto);
			grid-gap: 10px;
			margin-top: 15px;
		}

		.photo-box div img {
			width: 100%;
			cursor: pointer;
		}

		.profile-intro p {
			font-size: 14px;
		}

		.friends-box {
			display: grid;
			grid-template-columns: repeat(3, auto);
			grid-gap: 10px;
			margin-top: 15px;
		}

		.friends-box div img {
			width: 100%;
			cursor: pointer;
			padding-bottom: 20px;
		}

		.friends-box div {
			position: relative;
		}

		.friends-box p {
			position: absolute;
			bottom: 0;
			left: 0;
		}
	</style>



</head>

<body>

	<!------------HEADER-------------->
	<nav>
		<div class="nav-left">
			<img src="images/logo/fb.png" class="logo">
			<div class="search-box">
				<img src="images/search.png">
				<input type="text" placeholder="Search">
			</div>
		</div>

		<div class="nav-center">
			<ul>
				<a href="home.php">
					<li class="border-line-bottom  line1 "><img src="images/logo/home.png" alt=""></li>
				</a>
				<a href="">
					<li class="line1 border-line-bottom"><img src="images/logo/video.png" alt=""></li>
				</a>
				<a href="">
					<li class="line1 border-line-bottom"><img src="images/logo/marketplaces.png" alt=""></li>
				</a>
				<a href="">
					<li class="line1 border-line-bottom"><img src="images/logo/peoples.png" alt=""></li>
				</a>
				<a href="">
					<li class="line1 border-line-bottom"><img src="images/logo/menus.png" alt=""></li>
				</a>

			</ul>
		</div>

		<div class="nav-right">



			<ul>
				<div class="background-circle">
					<a href="home.php">
						<li class=""><img src="images/logo/menu_black.png" alt=""></li>
					</a>
				</div>
				<div class="background-circle">
					<a href="home.php">
						<li class=""><img src="images/logo/messenger_black.png" alt=""></li>
					</a>
				</div>
				<div class="background-circle">
					<a href="home.php">
						<li class=""><img src="images/logo/bell_black.png" alt=""></li>
					</a>
				</div>
			</ul>


			<div class="nav-user-icon online" onclick="settingsMenuToggle()">
				<img src="upload/<?php echo $row['filename']; ?>"
					onerror="if (this.src != 'upload/family.jpg') this.src = 'upload/family.jpg';">
			</div>

		</div>

		</div>
		<!------------------SETTINGS MENU------------------>
		<div class="settings-menu">


			<div id="dark-btn">
				<span></span>
			</div>


			<div class="settings-menu-inner">
				<div class="user-profile">
					<img src="upload/<?php echo $row['filename']; ?>"
						onerror="if (this.src != 'upload/default-pp.png') this.src = 'upload/default-pp.png';">
					<div class="">
						<p>
							<?php echo $row['f_name'] . " " . $row['l_name'] ?>
						</p>
						<a href="profile.php">See your profile</a>
					</div>
				</div>
				<hr>
				<div class="user-profile">
					<img src="images/feedback.png" alt="">
					<div class="">
						<p>Give feedback</p>
						<a href="#">Help us to improve</a>
					</div>
				</div>
				<hr>
				<div class="settings-links">
					<img src="images/setting.png" class="settings-icon">
					<a href="trialhome.php">Settings & Privacy <img src="images/arrow.png" width="10px"></a>
				</div>
				<div class="settings-links">
					<img src="images/help.png" class="settings-icon">
					<a href="">Help & Support <img src="images/arrow.png" width="10px"></a>
				</div>
				<div class="settings-links">
					<img src="images/display.png" class="settings-icon">
					<a href="">Display & Access <img src="images/arrow.png" width="10px"></a>
				</div>
				<div class="settings-links">
					<img src="images/logout.png" class="settings-icon">
					<a href="logout.php">Logout <img src="images/arrow.png" width="10px"></a>
				</div>


			</div>
		</div>

	</nav>

	<!-----POFILE PAGE---->
	<div class="profile-container">
		<img src="images/cover.jpg" class="cover-img">
		<div class="profile-details">
			<div class="pd-left">
				<div class="pd-row">
					<!---PROFILE PICTURE-->
					<img src="upload/<?php echo $row['filename']; ?>"
						onerror="if (this.src != 'upload/default-pp.png') this.src = 'upload/default-pp.png';"
						class="pd-image">

					<div>
						<h3>
							<?php echo $row['f_name']; ?>
							<?php echo $row['l_name']; ?>
						</h3>
						<p>1.8K Followers - 120 Following</p>
						<img src="images/joann.jpg">
						<img src="images/jagdon.jpg">
						<img src="images/alvie_profile.jpg">
						<img src="images/Fredrick_profile.jpg">
					</div>
				</div>
			</div>
			<div class="pd-right">
				<button type="button"> <img src="images/add-friends.png"> Friends</button>
				<button type="button"> <img src="images/message.png"> Message</button>
				<br>
				<a href=""><img src="images/more.png"></a>
			</div>
		</div>

		<div class="profile-info">
			<div class="info-col">


				<div class="profile-intro">
					<h3>Intro</h3>
					<p class="intro-text">Finding solace in the pages of books and the whispers of thoughts✨📚
						<img src="images/feeling.png">
					</p>
					<hr>
					<ul>
						<li><img src="images/profile-study.png"> Bantayan Cebu</li>
						<!--PUT THE ADRESS HERE-->
						<li><img src="images/profile-home.png"> Lives in
							<?php echo $row['address']; ?>
						</li>
						<li><img src="images/profile-location.png"> From
							<?php echo $row['address']; ?>
						</li>
						<li> <img src="images/confetti.png">Birthday :
							<?php echo $row['bdate_mnt']; ?> /
							<?php echo $row['bdate_day']; ?> /
							<?php echo $row['bdate_year']; ?>
						</li>
						<li> <img src="images/gender.png">Gender :
							<?php echo $row['gender']; ?>
						</li>
					</ul>
				</div>

				<!------NEED TO CHANGE THIS PHOTOS------->
				<div class="profile-intro">
					<div class="title-box">
						<h3>Photos</h3>
						<a href="#">All Photos</a>
					</div>
					<div class="photo-box">
						<div><img src="upload/<?php echo $row['filename']; ?>"
								onerror="if (this.src != 'upload/default-pp.png') this.src = 'upload/default-pp.png';"
								alt=""></div>
						<div><img src="images/fariola-profile1.jpg" alt=""></div>
						<div><img src="images/fariola-profile2.jpg" alt=""></div>
						<div><img src="images/fariola-profile3.jpg" alt=""></div>
						<div><img src="images/fariola-profile4.jpg" alt=""></div>
						<div><img src="images/fariola-profile5.jpg" alt=""></div>
					</div>
				</div>

				<!--------------------------ADD DIFFERENT FRIENDS PHOTO------------------------------->
				<div class="profile-intro">
					<div class="title-box">
						<h3>Friends</h3>
						<a href="#">All Friends</a>
					</div>
					<p>120 (10 mutual)</p>
					<div class="friends-box">
						<div><img src="images/joann.jpg" alt="">
							<p>Joann Rebamonte</p>
						</div>
						<div><img src="images/jagdon.jpg" alt="">
							<p>John Carlo Jagdon</p>
						</div>
						<div><img src="images/joann_post.jpg" alt="">
							<p> Joann Rebamonte II </p>
						</div>
						<div><img src="images/Fredrick_profile.jpg" alt="">
							<p> Fredrick Allan </p>
						</div>
						<div><img src="images/alvie_story.jpg" alt="">
							<p> Alvie Thompson </p>
						</div>
						<div><img src="images/alex_profile.jpg" alt="">
							<p> Alex Smith </p>
						</div>
					</div>
				</div>



			</div>
			<div class="post-col">
				<div class="write-post-container">
					<div class="user-profile">
						<img src="upload/<?php echo $row['filename']; ?>"
							onerror="if (this.src != 'upload/default-pp.png') this.src = 'upload/default-pp.png';">
						<div>
							<p>
								<?php echo $row['f_name']; ?>
								<?php echo $row['l_name']; ?>
							</p>
							<small>Public <i class="fas fa-caret-down"></i> </small>
						</div>
					</div>

					<div class="post-input-container">
						<textarea rows="3" placeholder="What's on your mind <?php echo $row['f_name']; ?>? "></textarea>
						<div class="add-post-links">
							<a href="#"><img src="images/live-video.png"></a>
							<a href="#"><img src="images/photo.png"></a>
							<a href="#"><img src="images/feeling.png"></a>
						</div>




					</div>


				</div>

				<!-----------FIRST vIDEO----------->

				<div class="post-container">
					<div class="user-profile">
						<img src="upload/<?php echo $row['filename']; ?>"
							onerror="if (this.src != 'upload/family.jpg') this.src = 'upload/family.jpg';">
						<div class="">
							<p>
								<?php echo $row['f_name']; ?>
								<?php echo $row['l_name']; ?>
							</p>
							<span>February 14 at 7:55 PM</span>
						</div>
					</div>
					<p class="post-text">❤️🥰 <a href=""><!--FOR POSTS WITH HASHTAGS--></a></p>
					<video controls class="post-img" autoplay muted>
						<source src="video/work.mp4" type="video/mp4">

					</video>

					<div class="post-row">
						<div class="activity-icons">
							<div>
								<img src="images/like.png" alt="">120k
							</div>
							<div>
								<img src="images/comments.png" alt="">10k
							</div>
							<div>
								<img src="images/share.png" alt="">20k
							</div>
						</div>
						<div class="post-profile-icon">
							<img src="upload/<?php echo $row['filename']; ?>"
								onerror="if (this.src != 'upload/family.jpg') this.src = 'upload/family.jpg';">

						</div>
					</div>

				</div>

				<!-----------FIRST IMAGE----------->

				<div class="post-container">
					<div class="post-row">
						<div class="user-profile">
							<img src="upload/<?php echo $row['filename']; ?>"
								onerror="if (this.src != 'upload/default-pp.png') this.src = 'upload/default-pp.png';">
							<div>
								<p><b>
										<?php echo $row['f_name']; ?>
										<?php echo $row['l_name']; ?>
									</b> updated his profile picture</p>
								<span>October 27 2022, 11:30 pm</span>
							</div>
						</div>
						<!--NEED TO DOWNLOAD THE-->
						<a href="#"><i class="fas fa-ellipsis-v"></i></a>
					</div>

					<div class="post-col">

						<div class="write-post-container">


							<img src="upload/<?php echo $row['filename']; ?>" class="post-img"
								onerror="if (this.src != 'upload/default-pp.png') this.src = 'upload/default-pp.png';">
							<div class="post-row">
								<div class="activity-icons">
									<div><img src="images/like.png">120</div>
									<div><img src="images/comments.png">50</div>
									<div><img src="images/share.png">10</div>
								</div>
								<div class="post-profile-icon">
									<img src="upload/<?php echo $row['filename']; ?>"
										onerror="if (this.src != 'upload/default-pp.png') this.src = 'upload/default-pp.png';">
								</div>

							</div>

						</div>

					</div>


				</div>








				<!-----------SECOND IMAGE----------->
				<div class="post-container">
					<div class="post-row">
						<div class="user-profile">
							<img src="upload/<?php echo $row['filename']; ?>"
								onerror="if (this.src != 'upload/default-pp.png') this.src = 'upload/default-pp.png';">
							<div>
								<p><b>
										<?php echo $row['f_name']; ?>
										<?php echo $row['l_name']; ?>
									</b> </p>
								<span>October 20 2022, 11:30 am</span>
							</div>
						</div>
						<!--NEED TO DOWNLOAD THE-->
						<a href="#"><i class="fas fa-ellipsis-v"></i></a>
					</div>

					<div class="post-col">

						<div class="write-post-container">


							<img src="images/fariola-profile1.jpg" class="post-img">
							<div class="post-row">
								<div class="activity-icons">
									<div><img src="images/like.png">120</div>
									<div><img src="images/comments.png">50</div>
									<div><img src="images/share.png">10</div>
								</div>
								<div class="post-profile-icon">
									<img src="upload/<?php echo $row['filename']; ?>"
										onerror="if (this.src != 'upload/default-pp.png') this.src = 'upload/default-pp.png';">
								</div>

							</div>
						</div>
					</div>
				</div>
				<!-----------THIRD IMAGE----------->
				
				<!-----------FOURTH IMAGE----------->
				
				<!-----------FIFTH IMAGE----------->
				
				<!-----------SIXTH IMAGE----------->
			

				<div class="footer">
					<p>Copright 2023 - for secure programming only</p>
				</div>
				<script src="home.js"></script>
</body>

</html>