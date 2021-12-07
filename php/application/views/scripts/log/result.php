<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb3212">
<title>日志搜索结果页面</title>
<link href="<?php echo $this->baseUrl;?>css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="showext" style="z-index:999; display:none;position:absolute; background-color:#FFFFFF">
<div id="showext_detail"></div>
<input type="button" class="btn_std" value="关闭" name="button_sd" id="button_sd"/>
</div>
<form method=post action="<?php echo $this->baseUrl;?>log/delete">
<input type=hidden name="nowp" value="<?php echo $this->nowp;?>" />
<table width="100%" border="1" class="tableA">
  <tr>
    <td width="5%">&nbsp;</td>
    <td width="11%" align="center"><strong>用户名</strong></td>
    <td width="19%" align="center"><strong>控制器</strong></td>
    <td width="20%" align="center"><strong>方法</strong></td>
    <td width="16%" align="center"><strong>IP</strong></td>
    <td width="19%" align="center"><strong>时间</strong></td>
    <td width="10%" align="center"><strong>详细</strong></td>
  </tr>
<!--
<?php
foreach ($this->list as $k=>$v) {
$v['ip']   = long2ip($v['ip']);
$v['time'] = date ('Y/m/d H/i/s', $v['time']);
print <<<EOT
-->
  <tr>
    <td align="center" nowrap><input type="checkbox" name="alid[]" value="{$v['alid']}"></td>
    <td nowrap>{$v['username']}</td>
    <td nowrap>{$v['controller']}</td>
    <td nowrap>{$v['action']}</td>
    <td nowrap>{$v['ip']}</td>
    <td nowrap>{$v['time']}</td>
    <td align="center" nowrap class='showdetail' id="detail_{$v['alid']}"><a href="#">查看</a></td>
  </tr>
<!--
EOT;
}?>-->
  <tr>
    <td colspan="7"><input name="sall" id="sall" type="button" class="btn_std" value="全选">
    <input name="sopp" id="sopp" type="button" class="btn_std" value="反选">
    <input name="del" id="del" type="button" class="btn_std_XXL" value="选中项删除"></td>
  </tr>
<!--
<?php
print <<<EOT
-->
  <tr>
          <td colspan="7"><a href="{$this->baseUrl}log/result/nowp/1">首页</a> <a href="{$this->baseUrl}log/result/nowp/{$this->page['previous']}">前一页</a>
<!--
EOT;
foreach ($this->pagenum as $v){
if ($this->page['nowp'] == $v)
print <<<EOT
-->
&nbsp;<font color="red">{$v}</font>&nbsp;
<!--
EOT;
else
print <<<EOT
-->
&nbsp;<a href="{$this->baseUrl}log/result/nowp/{$v}">{$v}</a>&nbsp;
<!--
EOT;
}
print <<<EOT
-->
<a href="{$this->baseUrl}log/result/nowp/{$this->page['next']}">下一页</a> <a href="{$this->baseUrl}log/result/nowp/{$this->page['totalpage']}">末页</a></td>
  </tr>
<!--
EOT;
?>-->

</table>
</form>
</body>
<script language="javascript">
var baseUrl = '<?php echo $this->baseUrl;?>';
</script>
<script language="javascript" src="<?php echo $this->baseUrl;?>js/jquery.pack.js" type="text/javascript" ></script>
<script language="javascript" src="<?php echo $this->baseUrl;?>js/log_result.js" type="text/javascript" ></script>
</html>
