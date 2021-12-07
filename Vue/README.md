开发地址注意和datatest/application/controllers/Admin_Action.php中的Access-Control-Allow-Origin选项值一致

添加新模块：
1.router添加路由
2.store添加侧边栏
3.数据库privilegegroup中第一行添加新模块的url，注意以逗号结尾

把生成的dist文件夹中除index.html以外的文件直接拷贝到htdocs目录中，修改index.html为index.php，把index.php覆盖到application/views/scripts/index/index.php