<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission5-2</title>
</head>
<body>

<?php
// DB接続設定
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
//mission5-1というテーブル作成 →確認済み   
    $sql = "CREATE TABLE IF NOT EXISTS mission5_1"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"//自動のナンバリング
	. "name char(32),"//名前を入れる、半角英数32文字
	. "comment TEXT,"//コメントを入れる
	. "password char(32),"//パスワードを入れる
	. "date TEXT"//投稿日時を入れる
	.");";
    $stmt = $pdo->query($sql);
    

//削除ボタンが押されたとき
    if(isset($_POST["delete"])){
        //削除番号とidを比べて抽出
        $id=$_POST["delete-number"];
        $dpass=$_POST["d-password"];
        $sql = 'SELECT * FROM mission5_1 WHERE id=:id';
    	$stmt = $pdo->prepare($sql);
    	$stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        $stmt->execute(); 
        $results = $stmt->fetchAll();
        
        //抽出されたデータのパスワードを$passwordに代入
    	foreach ($results as $row){
    		$password = $row['password'];
        }
        
        //$passwordと削除パスワードが一致したら削除
        if($dpass==$password){
        	$sql = 'delete from mission5_1 where id=:id';
        	$stmt = $pdo->prepare($sql);
        	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
        	$stmt->execute();
        }
        
//編集ボタンが押されたら
    }elseif(isset($_POST["edit"])){
        //編集番号とidを比べて抽出
        $id=$_POST["edit-number"];
        $epass=$_POST["e-password"];
        $sql = 'SELECT * FROM mission5_1 WHERE id=:id';
    	$stmt = $pdo->prepare($sql);
    	$stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        $stmt->execute(); 
        $results = $stmt->fetchAll();
        //抽出されたパスワードと編集パスワードが一致したら
        //編集番号と名前とコメントをフォームに入れる
    	foreach ($results as $row){
    	    if($epass==$row['password']){
        		$enum=$row['id'];
        		$ename = $row['name'];
        		$ecom = $row['comment'];
    	    }
        }
    }
    
    
//送信ボタンが押されたら
//名前とコメントとパスワードが投稿されたとき
//新規投稿か編集
    elseif(isset($_POST["submit"])){
        //editが空でない場合→編集する
        if($_POST["edit"]){
            $id=$_POST["edit"];
            $name=$_POST["name"];
            $comment=$_POST["comment"];
            $date=date("Y/m/d H:i:s");
            
            //編集
            $sql = 'UPDATE mission5_1 SET name=:name,comment=:comment,date=:date WHERE id=:id';
        	$stmt = $pdo->prepare($sql);
        	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
        	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        	$stmt->bindParam(':date', $date, PDO::PARAM_STR);
        	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
        
        //editが空の場合→新規投稿
        else{
            $sql = $pdo -> prepare("INSERT INTO mission5_1 (name, comment,date,password) VALUES (:name, :comment,:date,:password)");
        	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
        	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
        	$sql -> bindParam(':password', $password, PDO::PARAM_STR);
        	$name = $_POST["name"];
        	$comment = $_POST["comment"];
        	$date=date("Y/m/d H:i:s");
            $password=$_POST["s-password"];
        	$sql -> execute();
        }
    }
?>

<form action=""method="post">
        【投稿フォーム】<br>
        <input type="hidden" name="edit"
        value=<?php if(isset ($enum)){echo $enum;}?>>
        <input type="text" name="name" placeholder="名前" 
        value=<?php if(isset($ename)){echo $ename;}?>>
        <input type="text" name="comment" placeholder="コメント"
        value=<?php if(isset($ecom)){echo $ecom;}?>>
        <input type="text" name="s-password" placeholder="パスワード">
        <input type="submit" name="submit"><br>
        
        【削除フォーム】<br>
        <input type="number" name="delete-number" placeholder="削除番号">
        <input type="text" name="d-password" placeholder="パスワード">
        <input type="submit" name="delete" value="削除"><br>
 
        【編集フォーム】<br>
        <input type="number" name="edit-number" placeholder="編集対象番号">
        <input type="text" name="e-password" placeholder="パスワード">
        <input type="submit" name="edit" value="編集">
</form>

<?php
//データベース内のデータ全てを表示
	$sql = 'SELECT * FROM mission5_1';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	echo "<br>【投稿一覧】<br>";
	foreach ($results as $row){
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
	echo "<hr>";
	}
?>

</body>
</html>