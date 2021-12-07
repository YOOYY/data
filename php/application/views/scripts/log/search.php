<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>日志搜索</title>
<link href="<?php echo $this->baseUrl;?>css/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo $this->baseUrl;?>css/calendar.css" rel="stylesheet" type="text/css">
</head>

<body>
<form method="post" action="<?php echo $this->baseUrl;?>log/result" >
<table width="100%" border="1" class="tableA">
  <tr>
    <td colspan="2" align="center"><strong>系统日志搜索，若条件不填，则为匹配所有</strong></td>
  </tr>
  <tr>
    <td width="17%">管理用户名</td>
    <td width="83%"><input name="username" type="text" id="username" size="15" maxlength="15"></td>
  </tr>
  <tr>
    <td>登陆ip</td>
    <td><input name="ip" type="text" id="ip" size="15" maxlength="15"></td>
  </tr>
  <tr>
    <td>控制器名称</td>
    <td><input name="controller" type="text" id="controller" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <td>方法名称</td>
    <td><input name="action" type="text" id="action" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <td>日志时间</td>
    <td><input name="stime" type="text" id="stime" size="15" maxlength="15" readonly="true">
    -
    <input name="etime" type="text" id="etime" size="15" maxlength="15" readonly="true"></td>
  </tr>
  <tr>
    <td>日志内容</td>
    <td><input name="ext" type="text" id="ext" size="30" maxlength="50"></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input name="search" type="submit" class="btn_submit" value="确定"></td>
  </tr>
</table>
</form>
</body>
<script language="javascript" src="<?php echo $this->baseUrl;?>js/jquery.pack.js" type="text/javascript" ></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>js/Hg.Calendar.js"></script>
<script language="javascript" src="<?php echo $this->baseUrl;?>js/log_search.js" type="text/javascript" ></script>
</html>
