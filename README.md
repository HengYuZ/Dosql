#Dosql
##简介
##Dosql是一个简单的mysql操作数据库封装类。

##使用
  新建一个composer.json文件
  ```
      {
        "require":{
            "php":">=5.3.0",
            "hengyuz/dosql":"dev-master"
        },
        "minimum-stability":"stable"
      }
  ```
  然后保存composer.json文件后，使用composer install 命令进行安装。

  然后使用命令: ```cd vendor/hengyu/dosql```;

  简单使用可以参照show.php。
##更多用法：
  Dosql不使用组合条件：
  ```
  1. $dosql->select('user','','');
  2. $dosql->select('user','',['id='=>9]);
  ```
  Dosql支持组合条件使用：
  ```
  1. $dosql->select('tb_name',['name','id'],['AND'=>[['id='=>9],['name'=>'hengyu']);
  2. $dosql->select('tb_name',['name','id'],['AND'=>['OR'=>[['id='=>9],['name='=>'hengyu']],'OR'=>[['id='=>5],['name='=>'zhu']]]];
  ```
