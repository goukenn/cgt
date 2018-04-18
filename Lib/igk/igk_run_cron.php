<?php
//run cron
//file: igk_run_cron.php
//desc: definition of run cron script
//date: 20-11-2017



include(".igk_include_header.pinc");
igk_ilog("run cron");
igk_getctrl(IGK_SESSION_CTRL)->RunCron();
?>