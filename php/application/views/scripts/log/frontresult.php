<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312"/>
<title>前台日志显示</title>
</head>

<body>
<?php if($this->exist == 'ok'){?>
<div style="padding:10px;text-align:center">前台日志显示（<?php echo $this->sdate;?>）</div>
<div style="border:1px yellow solid; padding:5px;"><?php echo nl2br($this->content);?></div>
<?php }else{?>
<div style="padding:10px;text-align:center;color:red;">该天暂无前台日志（<?php echo $this->sdate;?>）</div>
<?php }?>
</body>
</html>