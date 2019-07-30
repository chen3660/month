<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/30 0030
 * Time: 16:15
 */
$redis = new Redis();
$redis -> connect('127.0.0.1',6379);

$page = isset($_GET['page'])?$_GET['page']:1;
$type = isset($_GET['type'])?$_GET['type']:'';
$title = isset($_GET['title'])?$_GET['title']:'';

if($title == ''&& $type == ''&&$redis -> hExists('data',"$page")){


        $all = $redis->hGet('data', "$page");

        $data = json_decode($all, true);

}else{
    $dbh = new PDO('mysql:host=localhost;dbname=month', 'root', 'root');

    $where = '1=1';

    if ($type != ''){
        $where .= " and books_type like '%$type%'";
    }
    if ($title != ''){
        $where .= " and books_title like '%$title%'";
    }

    $sqlAll = "select * from books where $where";

    $resAll = $dbh -> query($sqlAll);

    $count = $resAll -> rowCount($sqlAll);

    $size = 6;

    $limit = ($page-1)*$size;

    $pageCount = ceil($count/$size);

    $sqlAll1 = "select * from books where $where limit $limit,$size";

    $resAll1 = $dbh -> query($sqlAll1);

    $dataAll = $resAll1 -> fetchAll(2);

    $prev = $page-1 < 1 ? 1 : $page - 1;
    $next = $page +1 > $pageCount ? $pageCount : $page + 1;

    $data = [
        'data' => $dataAll,
        'prev' => $prev,
        'last' => $pageCount,
        'next' => $next
    ];
    $dataone  = json_encode($data);


    $redis -> hSet('data',"$page",$dataone);

}




?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css">
    <script src="http://apps.bdimg.com/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="http://apps.bdimg.com/libs/bootstrap/3.3.0/js/bootstrap.min.js"></script>

</head>
<body>
    <div class="container">
        <form action="show.php">
            标题<input type="text" name="title">
            分类<input type="text" name="type">
            <input type="submit" value="搜索">
        </form>
        <table class="table table-striped">
            <caption>展示页面</caption>

            <thead>
            <tr>
                <th>小说编号</th>
                <th>小说标题</th>
                <th>小说网址</th>
                <th>小说类型</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>

                <?php
                    foreach($data['data'] as $k => $v){?>
                        <tr>
                            <td><?php  echo $v['books_id'];  ?></td>
                            <td><?php  echo $v['books_title'];  ?></td>
                            <td><?php  echo $v['books_href'];  ?></td>
                            <td><?php  echo $v['books_type'];  ?></td>
                            <td><a href="showinfo.php?id=<?php  echo $v['books_id'];  ?>">查看详情</a></td>
                        </tr>
                <?php  }  ?>

            </tbody>
        </table>
        <div class="btn-group">
            <button type="button" class="btn btn-default"><a href="showbooks.html?page=1">首页</a></button>
            <button type="button" class="btn btn-default"><a href="showbooks.html?page=<?php  echo $data['prev'];  ?>">上一页</a></button>
            <button type="button" class="btn btn-default"><a href="showbooks.html?page=<?php  echo $data['next'];  ?>">下一页</a></button>
            <button type="button" class="btn btn-default"><a href="showbooks.html?page=<?php  echo $data['last'];  ?>">尾页</a></button>
        </div>

    </div>

</body>
</html>


