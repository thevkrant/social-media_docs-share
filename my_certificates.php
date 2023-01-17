<?php include_once('../header/userHeader.php');
if (isset($_REQUEST['cid'])) {
	$course_id = trim(filter_query($_REQUEST['cid']));
}
if (isset($_REQUEST['lid'])) {
	$levelId = trim(filter_query($_REQUEST['lid']));
	$level = 'Module ' . $levelId;
}

?>

<section class="scrollable padderNone">
	<div class="moduleHeader relative">
		<div class="learnWel  hide" id="learnWel">
			<div class="leftSide">
				<div class="welcomeDiv">
					<?php echo $level; ?>: <?php echo $language[$_SESSION['language']]['my_certificates']; ?>
				</div>
			</div>

			<div class="showLevel  hide showDown" onclick="showHideLevel('learnWel','levelDiv','1')">
				<img style='display:none;' src="<?php echo $_html_relative_path; ?>images/slideDown.png" />
				<div style="display: none;float: left;color: #111;padding: 20px 5px;font-size:13px"><?php echo $language[$_SESSION['language']]['module']; ?> <?php echo $language[$_SESSION['language']]['selection']; ?></div>
			</div>

			<div class="rightSide">
				<div class="pull-right rightImg"></div>
				<div class="clear"></div>
			</div>

			<div class="moduleRightOtherBg moduleRightOtherBgSkill">
				<div class="moduleRightMidOtherBg moduleRightMidOtherBgSkill">&nbsp;</div>
			</div>
		</div>

		<div class="levelDiv" id="levelDiv">
			<div class="showLevel showUp" onclick="showHideLevel('learnWel','levelDiv','2')">
				<div style="display: none;float: left;color: #111;padding: 20px 5px;font-size:13px"><?php echo $language[$_SESSION['language']]['module']; ?> <?php echo $language[$_SESSION['language']]['selection']; ?></div><img class="" src="<?php echo $_html_relative_path; ?>images/slideUp.png" style="display:none;padding-top: 15px;" />
			</div>
			<div class="levels" style='width:300px;'> <?php echo $language[$_SESSION['language']]['my_certificates']; ?></div>
			<div class="levelsRange" style='display:none'>
				<ul>
					<?php
					$i = 1;

					foreach ($courseRangeArr as $key => $levelValue) {
						if ($i < 10) {
							$courseCount = "0" . $key;
							$crsCount = $key;
						} else {
							$courseCount = $key;
							$crsCount = $key;
						}
						$link = $levelValue;
						if ($getRange == $key) {
							$active = "active";
						} else {
							$active = "";
						}
						if ($key == $visitLevel) {
							$activeShow = "show";
						} else {
							$activeShow = "";
						}

					?>
						<li id="lpath<?php echo $i; ?>" path="<?php echo $lpath; ?>" onclick="visitLevel(this.id,'<?php echo $link; ?>','<?php echo $key; ?>');" class="<?php echo $hideColor . " " . $activeShow . ' ' . $active; ?>" link="<?php echo $link; ?>"><a href="javascript:void(0)" <?php echo $disable; ?>><?php echo $courseCount; ?></a></li>
					<?php $i++;
					} ?>
				</ul>
			</div>
		</div>
	</div>
	<div class="clear"></div>

	<div class="padder20 top0" style="padding-top:20px;">
		<div class="allTopicDiv">
			<?php

			$getAssignProductInfo1 = $centerObj->getBatchDataByIDDetails($batch_id, $center_id, '');
			// echo "<pre>";print_r($getAssignProductInfo1); 
			$courseArr1 = array();
			foreach ($getAssignProductInfo1 as $key => $val) {

				$batchCourseStr2 = str_replace("CRS-", "", $val['course']);
				$courseArrData = $adminObj->getCustomCourseList($courseType, $batchCourseStr2, '');

				$courseArr1[] = $courseArrData;
			}
			$courseName1 = "CDP COURSE";

			$courseArr1 = $courseArr1[0];

			if (count($courseArr1) > 0) {
				$i = 1;

				$completeCoursePerArr = array();
				foreach ($courseArr1 as $key => $val) {

					$arrCourse = array();
					$arrCourse['edge_id'] = $val->edge_id;
					$arrCourse['userToken'] = $userToken;
					$arrCourse['package_code'] = $package_code;
					$arrCourse['course_code'] = '';
					$arrCourse['center_id'] = $center_id;
					$arrCourse['batch_id'] = $batch_id;
					$completeCourseArr =  $objTR->getCompletion($arrCourse);
					//echo "<pre>";print_r($completeCourseArr);
					$completeCoursePer = $completeCourseArr['complete_per'];
					$completeCourseStatus = $completeCourseArr['completion_status'];
					$completeCoursePer = 100;
					if ($completeCoursePer == 100) {
						$completeCoursePerArr[] = $completeCoursePer;

			?>
						<div class="topicHead topicHeadDefault100" title="<?php echo $val->desc; ?>" style="display:none">
							<div id="module<?php echo $i; ?>">
								<div class="topicImg">
									<div class="topicImgBg"><img class="imgTopc" src="<?php echo $courseImg; ?>" style="width:60px;" /> </div>
									<div class="title" style="font-size: 14px;font-weight: normal;">
										<span class="leftText relative">
											<div class="topicCount" style="height: 120px;font-size: 38px;"></div>
											<?php echo $val->name; ?><br />
											<div class="clear"></div>
											<div class="progressDiv" completeCourseStatus="<?php echo $completeCourseStatus; ?>" id="progressDiv<?php echo $i; ?>" count="<?php echo $completeCoursePer; ?>">
												<div class="empty"></div>
												<?php if ($completeCoursePer == 0 && $completeCoursePer == '') { ?>
													<div class="scoreFill" style="width:<?php echo '0%'; ?>"></div>
												<?php } else {  ?>
													<div class="scoreFill" style="width:<?php echo $completeCoursePer . '%'; ?>"></div>
												<?php } ?>
											</div>
											<div class="clear"></div>
											<?php if ($completeCoursePer == 100 && $completeCoursePer == '') { ?>
												<span class="pull-left" style="display:none"><strong> <?php echo 'score'; ?>: <div class="perDiv text-left" style="margin-right:10px;font-weight:normal;display: inline-block;" id="scoreDiv<?php echo $i; ?>"><?php echo '0%' ?></div></strong>
												</span>
												<span class="pull-right"><strong> <?php echo $language[$_SESSION['language']]['progress']; ?>: <div class="perDiv text-left" style="margin-right:10px;font-weight:normal;display: inline-block;" id="perDiv<?php echo $i; ?>"><?php echo '0%' ?></div></strong>
												</span><?php } else {  ?>
												<span class="pull-left" style="display:none"><strong> <?php echo 'score'; ?>: <div class="perDiv text-left" style="margin-right:10px;font-weight:normal;display: inline-block;" id="scoreDiv<?php echo $i; ?>"><?php echo (!empty($completeCoursePer)) ? $completeCoursePer : "0"; ?>%</div></strong>
												</span>
												<span class="pull-right"><strong> <?php echo $language[$_SESSION['language']]['progress']; ?>: <div class="perDiv  text-left" style="margin-right:10px;font-weight:normal;display: inline-block;" id="perDiv<?php echo $i; ?>"><?php echo $completeCoursePer . '%'; ?></div></strong>
												</span><?php } ?>
										</span>
										<span class="rightText pull-right text-center">
											<div class="clear"></div>
											<?php if ($completeCoursePer == $compareCountShow) { ?>
												<a href="javascript:open_cert('<?php echo base64_encode($course_id) ?>')" class="" style="margin-top:10px;width:90px;font-weight:bold" title="<?php echo '';   ?>"> <?php echo $language[$_SESSION['language']]['download']; ?> <?php echo $language[$_SESSION['language']]['certificate']; ?></a>
										</span>
									<?php } ?>
									</div>
								</div>
							</div>
							<div class="clear"></div>
						</div>
				<?php }
					$i++;
				} ?>
				<?php if (count($completeCoursePerArr) == count($courseArr1)) { ?>
					<!-- my certificate cdp-->
					<div class="topicHead topicHeadDefault100">
						<div>
							<div class="title" style="font-size: 14px;font-weight: normal;">
								<span class="leftText relative">
									<div class="topicCount" style="height: 120px;font-size: 38px;"></div>
									<?php echo $courseName1; ?><br />
									<div class="clear"></div>

									<div class="clear"></div>
								</span>
								<span class="rightText pull-right text-center" style="line-height: 25px;">
									<a href="javascript:open_cert('<?php echo base64_encode($user_id) ?>')" class="" style="margin-top:10px;width:90px;font-weight:bold" title="<?php echo '';   ?>"> <?php echo $language[$_SESSION['language']]['download']; ?> <?php echo $language[$_SESSION['language']]['certificate']; ?></a>
									<div class="clear"></div>
									<span class="component">
										<?php $cert_path_to_share = $globalLink . "/user/certificate-create-share.php?userRowId=" . base64_encode($user_id);
										?>

										<a class="button pointer" title="Twitter Share  Completion certificate for <?php echo $courseName1; ?>" data-sharer="twitter" data-title="Completion certificate for <?php echo $courseName1; ?>" data-hashtags="" data-url="<?php echo $cert_path_to_share; ?>"><i class="fa fa-twitter" style="font-size: 1.5em;color:rgb(29, 155, 240);margin-top: 10px;"></i></a>
										&nbsp; &nbsp;
										<a class="button pointer" title="Facebook Share Completion certificate for <?php echo $courseName1; ?>" data-sharer="facebook" data-quote="Completion certificate for <?php echo $courseName1; ?>" data-hashtag="" data-url="<?php echo $cert_path_to_share; ?>"><i class="fa fa-facebook" style="font-size: 1.5em;color: #4267b2;margin-top: 10px;"></i></a>

										&nbsp; &nbsp;
										<a class="button pointer" title="Linkedin Share Completion certificate for <?php echo $courseName1; ?>" data-sharer="linkedin" data-quote="Completion certificate for <?php echo $courseName1; ?>" data-hashtag="" data-url="<?php echo $cert_path_to_share; ?>"><i class="fa fa-linkedin" style="font-size: 1.5em;color: #4267b2;margin-top: 10px;"></i></a>

									</span>
								</span>
							</div>
						</div>
					<?php } else { ?>
						<div class="topic"><?php echo 'Certificate is currently not available. Please complete the all chapters to generate the certificate.'; ?></div>

					<?php } ?>
				<?php } else { ?>
					<div class="topic"><?php echo $language[$_SESSION['language']]['there_are_no_certificates_available_for_you']; ?></div>

				<?php } ?>
					</div>
		</div>
</section>
<?php include_once('../footer/userFooter.php'); ?>
<script>
	$(window).on('beforeunload', function() {
		try {
			//  $("#loaderDiv").show();

		} catch (e) {

		}

	});

	//to create and open certificate
	function open_cert(userRowId) {
		//alert(docId+"-"+cdate);
		var w = 1024;
		var h = 678;
		var winl = (screen.width - w) / 2;
		var wint = (screen.height - h) / 2;
		if (winl < 0) winl = 0;
		if (wint < 0) wint = 0;
		windowprops = "height=" + h + ",width=" + w + ",top=" + wint + ",left=" + winl + ",location=no," + "scrollbars=no,menubars=no,toolbars=no,resizable=no,status=no,directories=no";
		path = '<?php echo $globalLink ?>/user/certificate-create-share.php?userRowId=' + userRowId;
		var con_window = window.open(path, "win", windowprops);
		con_window.focus();
		//location.href=path;
	}
</script>
<script src="../js/sharer.min.js"></script>