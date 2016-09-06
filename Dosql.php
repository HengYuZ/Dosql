<?php
/**
 * Created by PhpStorm.
 * User: zhanghengyu
 * Date: 2016/9/6
 * Time: 21:06
 */
namespace mydb\db;
class Dosql
{
    //base data property
    protected $db_host;

    protected $username;

    protected $password;

    protected $db_name;

    protected $db_table;

    protected $tb_prefix = null;

    protected $charset = 'utf8';

    protected $type = 'mysql';

    protected $operate = array();

    protected $last_insert_id = null;   //最后一次插入的id

    protected $object = null;   //保存连接成功后的对象

    public function __construct($data = null)
    {
        if (is_array($data)) {
            foreach ($data as $property => $value)
                $this->$property = $value;
        } else {
            return false;
        }
        try {
            $connect = mysqli_connect($this->db_host, $this->username, $this->password, $this->db_name);
            if ($connect) {
                if ($this->charset) {
                    mysqli_set_charset($connect, $this->charset);
                }
                echo "连接数据库成功<br/>";
                if ($this->object != null) {
                    return $this->object;
                } else {
                    $this->object = $connect;
                    return $this->object;
                }
            }
        } catch (\Exception $e) {
            echo '连接数据库出错，错误信息：' . $e;
        }
    }

    protected function getLastInsertId()
    {
        $sql = "SELECT name FROM users;";
        $result = mysqli_query($this->object, $sql);
        // 返回记录数
        $rows_count = mysqli_num_rows($result);
        $rows_count += 1;
        return $rows_count;
    }

    //插入数据
    public function insert($tb_name, $data)
    {
        $values = '(';
        if ($this->last_insert_id == null) {
            $this->last_insert_id = $this->getLastInsertId();
        }
        $values .= $this->last_insert_id . ',';
        $this->db_table = $tb_name;
        if ($data) {
            foreach ($data as $key => $v) {
                if ($key < count($data) - 1) {
                    $values .= '"' . $v . '"' . ',';
                }
                if ($key == count($data) - 1) {
                    $values .= '"' . $v . '"';
                }
            }
            $values .= ')';
        } else {
            return false;
        }
        $sql = 'insert into ' . $this->tb_prefix . $this->db_table . ' values' . $values . '';
        try {
            $res = mysqli_query($this->object, $sql);
            $this->last_insert_id = mysqli_insert_id($this->object);
            if ($res) {
                echo '插入数据成功<br/>';
            } else {
                echo '插入数据失败<br/>';
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    //删除数据
    public function delete($tb_name, $data)
    {
        $condition = '';
        $first_item = true;
        if ($data) {
            $condition = $this->combineConditions($data);
            try {
                $sql = 'delete from ' . $this->tb_prefix . $tb_name . ' where ' . $condition;
                $res = mysqli_query($this->object, $sql);
                if ($res) {
                    echo '删除数据成功<br/>';
                } else {
                    echo '删除数据失败<br/>';
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    //查找数据
    public function select($tb_name, $field = null, $data)
    {

        $field_name = '';
        if ($field) {
            foreach ($field as $key => $v) {
                if ($key < count($field) - 1)
                    $field_name .= $v . ',';
                elseif ($key == count($field) - 1)
                    $field_name .= $v;
            }
        }
        $condition = $this->combineConditions($data);
        try {
            if ($field_name == '')
                $sql = 'select * from ' . $this->tb_prefix . $tb_name . ' where ' . $condition;
            elseif (count($field) >= 1) {
                $sql = 'select ' . $field_name . ' from ' . $this->tb_prefix . $tb_name . ' where ' . $condition;
            }
            $res = mysqli_query($this->object, $sql);
            if ($res) {
                echo '查找数据如下<br/>';
            } else {
                echo '查找数据失败<br/>';
            }
            $arr = array();
            while ($rows = mysqli_fetch_assoc($res)) {
                $arr[] = $rows;
            }
            return $arr;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    //修改数据
    public function update($tb_name, $field = null, $data)
    {
        $field_str = '';
        $first_item = true;
        if (!$field)
            return false;
        foreach ($field as $key => $v) {
            if ($first_item) {
                if (is_int($v)) {
                    $field_str .= $key . $v;
                } else if (is_string($v)) {
                    $field_str .= $key . "'" . $v . "'";
                }
                $first_item = false;
            } else {
                if (is_int($v))
                    $field_str .= ' and ' . $key . $v;
                else if (is_string($v))
                    $field_str .= ' and ' . $key . "'" . $v . "'";
            }
        }
        if ($data) {
            $condition = $this->combineConditions($data);
            $sql = 'update ' . $this->tb_prefix . $tb_name . ' set ' . $field_str . ' where ' . $condition;
            $res = mysqli_query($this->object, $sql);
            if ($res) {
                echo '修改数据成功<br/>';
            } else {
                echo '修改数据失败<br/>';
            }
        } else {
            return false;
        }
    }

    //组合传入的条件
    public function combineConditions($conditions)
    {
        $sql = '';
        if ($conditions) {
            foreach ($conditions as $operate => $condition) {
                if (is_array($condition)) {
                    if ($operate != '0')
                        $this->operate[] = $operate;
                    foreach ($condition as $key => $value) {
                        $data[][$key] = $value;
                    }
                } else {
                    $sql = $operate . '"' . $condition . '"';
                    return $sql;
                }
            }
            $first_item = true;
            $i = 0;
            foreach ($data as $key => $items) {
                if (is_array($items)) {
                    foreach ($items as $key => $item) {
                        $condition_num = count($item);
                        if (is_array($item)) {
                            foreach ($item as $key2 => $v) {
                                if ($first_item) {
                                    $sql .= $key2 . '"' . $v . '"';
                                    $first_item = false;
                                } else {
                                    if ($i % $condition_num == 0) {
                                        $sql .= ' ' . $this->operate[0] . ' ' . $key2 . '"' . $v . '"';
                                    } else
                                        $sql .= ' ' . $key . ' ' . $key2 . '' . '"' . $v . '"';
                                }
                                $i++;
                            }
                        } else {
                            if ($first_item) {
                                $sql .= $key . '"' . $item . '"';
                                $first_item = false;
                            } else {
                                $sql .= ' ' . $this->operate[0] . ' ' . $key . '"' . $item . '"';
                            }
                        }
                    }
                }
            }
            //echo $sql;
            return $sql;
        } else {
            return false;
        }
    }
}

$dosql = new Dosql(['localhost' => 'localhost', 'username' => 'root', '', 'db_name' => 'test']);
$res = $dosql->select('users', '', ['name=' => 'zhu']);
foreach ($res as $key => $item) {
    $i = 1;
    foreach ($item as $key2 => $value) {
        if ($i % count($item) == 0)
            echo $value . '<br/>';
        else
            echo $value . ' ';
        $i++;
    }
}
$dosql->update('users', ['name=' => 'zhu'], ['name=' => 'zhy']);
$dosql->insert('users', ['123456', '12346513@qq.com']);
$dosql->delete('users', ['OR' => ['name=' => '123456', 'email=' => 'hello.com']]);


