<?php
session_start();
if (!isset($_SESSION['login']))
{
	$_SESSION['login'] = 'guest';
}
require_once 'class.php';
$conn = new PDO('mysql:host=localhost;dbname=Coryphae', 'Roman', 'panshyn');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$tableWebPage = "WebPage";
$checkadmin = $conn->prepare("SELECT * from Users;");
$checkadmin->execute();
$checkadmin->setFetchMode(PDO::FETCH_ASSOC);
$resultadmin = $checkadmin->fetchAll();
foreach ($resultadmin as $key=>$row)
{
	$login = $row['Login'];
	$pass = $row['Password'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div id='logo'>
<img align='left' border=0 alt='Корифей продажа компьютерной техники' src='images/LogoCoryphae.jpg'>
<h1 align='center'>Магазин компьютерной техники Корифей</h1>
</div>
<div id=bottom>
<p>Roman Panshyn &copy; Copyright 2009-2018</p>
</div>
<?php
if (!isset($_GET['show']))
{
	echo "<title>Магазин компьютерной техники Корифей >> Товары</title>";
	$products = $conn->prepare("SELECT * FROM $tableWebPage where ParentId='root';");
	$products->execute();
	$products->setFetchMode(PDO::FETCH_ASSOC);
	$result = $products->fetchAll();	
	echo "<div id=content>";
	 echo "<h1>Корифей >> Список категорий</h1>";
	 echo "<table width=100% border=0 align=center>";	
		if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
		{		
			foreach($result as $row)
			{			
				$obj1 = new Get_List($row);
				$obj1->printCategoryAdmin();			
			}
		}
		if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
		{
			foreach($result as $row)
			{			
				$obj1 = new Get_List($row);
				$obj1->printCategory();			
			}
		} 
	 echo "</table>";
	echo "</div>";
	echo "<div id=navig>";
	 echo "<table width=100% border=0 align=left>";
	  echo "<a href='index.php?show=about'>Корифей > Контакты</a>";
		if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
		{
			foreach($result as $row)
			{			
				$obj1 = new Get_List($row);
				$obj1->printMenu();			
			}
		} 
		if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
		{
			foreach($result as $row)
			{			
				$obj1 = new Get_List($row);
				$obj1->printMenuAdmin();				
			}  
			echo "<tr><td>";
			echo "<a href='index.php?show=add_category'>Добавить категорию</a>";
			echo "</td></tr>";
		}
	 echo "</table>";
	echo "</div>";
}

if (isset($_GET['show']) && $_GET['show']=='add_category')
{
	 echo "<title>Добавить категорию</title>";	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		  echo "<div id=navig>";
		   echo "<a href='index.php'>Вернуться назад</a>";
		  echo "</div>";
		  echo "<div id=content>";
		   echo "<h2>Добавить новую категорию</h2>";
		   echo "<form action='index.php?show=added_category' align='left' method=POST>";
		    echo "<table>";
		     echo "<tr><td>Id</td><td><p><input type='text' name='id' size='50'/></p></td></tr>";
		     echo "<tr><td>Заголовок</td><td><p><input type='text' name='caption' size='50'/></p></td></tr>";
		     echo "<tr><td>Текст</td><td><p><textarea name='text' cols='40' rows='8'></textarea></p></td></tr>";
		     echo "<tr><td>Картинка</td><td><p><input type='text' name='image' size='50'/></p></td></tr>";
		     echo "<tr><td>Дата</td><td><p><input type='text' name='date' size='50'/></p></td></tr>";  
		    echo "</table>";
		    echo "<br><p align='center'><input type='submit' name='add' value='Добавить' />";
		   echo "</form>";
		  echo "</div>";
	 }
	 if ($_SESSION['login']!=$login&&$_SESSION['password']!=$pass)
	 {
		echo "<div id=content>";
		echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		echo "<a href='index.php'>Вернуться к списку категорий</a>";
		echo "</div>"; 
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='added_category')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 echo "<title>Добавлена категория ".$_POST['caption']."</title>";
		 echo "<div id=content>";
		 $sql = "INSERT INTO $tableWebPage values (";
		 $sql = $sql ."'root', '".$_POST['id'] . "', '". $_POST['caption']."', '". $_POST['caption']."', '". $_POST['text']."', '". $_POST['text']."', '".   	$_POST['image']."', '". $_POST['image']."', '". $_POST['date']."');";
		 $result = $conn->prepare($sql);
		 $result->execute();
		 if (!$result) echo "Не получилось добавить запись в $tableWebPage";
		 else 
		 {
			echo "Запись была успешно добавлена!<br>";
			echo "<a href='index.php'>Вернуться к списку категорий</a>";
		 }
		 echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		 echo "<div id=content>";
		 echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		 echo "<a href='index.php'>Вернуться к списку категорий</a>";
		 echo "</div>";
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='add_product')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 $sql = "SELECT ShortCaption FROM $tableWebPage where Id='".$_GET['category']."'";
		 $res = $conn->prepare($sql);
		 $res->execute();
		 $res->setFetchMode(PDO::FETCH_ASSOC);
		 $result = $res->fetchAll();
		 foreach ($result as $key=>$row)
		   $shortcaption = $row['ShortCaption'];
		 echo "<title>Добавить продукт в категорию ".$shortcaption."</title>";
		 echo "<div id=navig>";
		 echo "<a href='index.php?show=".$_GET['category']."'>Вернуться назад</a>";
		 echo "</div>";
		 echo "<div id=content>";
		  echo "<h2>Добавить новый продукт в категорию ".$shortcaption."</h2>";
		  echo "<form action='index.php?show=added_product&category=".$_GET['category']."' align='left' method=POST>";
		  echo "<table>";
		  echo "<tr><td>Id</td><td><p><input type='text' name='id' size='50'/></p></td></tr>";
		  echo "<tr><td>Заголовок</td><td><p><input type='text' name='caption' size='50'/></p></td></tr>";
		  echo "<tr><td>Короткий Заголовок</td><td><p><input type='text' name='shortcaption' size='50'/></p></td></tr>";
		  echo "<tr><td>Вступление</td><td><p><textarea name='intro' cols='40' rows='8'></textarea></p></td></tr>";
		  echo "<tr><td>Текст</td><td><p><textarea name='text' cols='40' rows='8'></textarea></p></td></tr>";
		  echo "<tr><td>Картинка</td><td><p><input type='text' name='image' size='50'/></p></td></tr>";
		  echo "<tr><td>Маленькая Картинка</td><td><p><input type='text' name='imagesmall' size='50'/></p></td></tr>";
		  echo "<tr><td>Дата</td><td><p><input type='text' name='date' size='50'/></p></td></tr>";
		  echo "</table>";
		  echo "<br><p align='center'><input type='submit' name='add' value='Добавить' />";
		  echo "</form>";
		 echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		  echo "<div id=content>";
		  echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		  echo "<a href='index.php'>Вернуться к списку категорий</a>";
		  echo "</div>";
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='added_product')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 $sql = "SELECT ShortCaption FROM $tableWebPage where Id='".$_GET['category']."'";
		 $res = $conn->prepare($sql);
		 $res->execute();
		 $res->setFetchMode(PDO::FETCH_ASSOC);
		 $result = $res->fetchAll();
		 foreach ($result as $key=>$row)
		   $resultPrev2 = $row['ShortCaption'];
		 echo "<title>Добавлен продукт ".$_POST['shortcaption']." в категорию ".$resultPrev2."</title>";
		 echo "<div id=content>";
		 $sql = "INSERT INTO $tableWebPage values ('".$_GET['category']."', '".$_POST['id'] . "', '". $_POST['caption']."', '". $_POST['shortcaption']."', '". 	$_POST['intro']."', '". $_POST['text']."', '". $_POST['image']."', '". $_POST['imagesmall']."', '". $_POST['date']."');";
		 $result = $conn->prepare($sql);
		 $result->execute();
		 if (!$result) echo "Не получилось добавить запись в $tableWebPage";
		 else 
		 {
			 echo "Запись была успешно добавлена!<br>";
			 echo "<a href='index.php?show=".$_GET['category']."'>Вернуться к списку продуктов</a>";
		 }
		 echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		  echo "<div id=content>";
		  echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		  echo "<a href='index.php'>Вернуться к списку категорий</a>";
		  echo "</div>";
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='edit_category')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 $sql = "SELECT * FROM $tableWebPage where Id='".$_GET['category']."';";
		 $res = $conn->prepare($sql);
		 $res->execute();
		 $res->setFetchMode(PDO::FETCH_ASSOC);
		 $result = $res->fetchAll();
		 foreach ($result as $key=>$val)
		 {
		   $shortcaption = $val['ShortCaption'];
		   $id = $val['Id'];
		   $caption = $val['Caption'];
		   $text = $val['Text'];
		   $image = $val['Image'];
		   $date = $val['Date'];
		 }
		 echo "<title>Изменить категорию ".$shortcaption."</title>";
		 echo "<div id=navig>";
		  echo "<a href='index.php'>Вернуться назад</a>";
		 echo "</div>";
		 echo "<div id=content>";		 
		  echo "<h2>Редактировать категорию ".$shortcaption."</h2>";
		  echo "<form action='index.php?show=edited_category&category=".$_GET['category']."' align='left' method=POST>";
		  echo "<table>";
		  echo "<tr><td>Id</td><td><p><input type='text' name='id' size='50' value='".$id."' /></p></td></tr>";
		  echo "<tr><td>Заголовок</td><td><p><input type='text' name='caption' size='50' value='".$caption."' /></p></td></tr>";
		  echo "<tr><td>Текст</td><td><p><textarea name='text' cols='40' rows='8'>".$text."</textarea></p></td></tr>";
		  echo "<tr><td>Картинка</td><td><p><input type='text' name='image' size='50' value='".$image."' /></p></td></tr>";
		  echo "<tr><td>Дата</td><td><p><input type='text' name='date' size='50' value='".$date."' /></p></td></tr>";
		  echo "</table>";
		  echo "<br><p align='center'><input type='submit' name='add' value='Сохранить' />";
		  echo "</form>";
		  echo "<p><a href='index.php?show=delete_category&category=".$_GET['category']."'>Удалить категорию</a></p>";
		 echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		 echo "<div id=content>";
		  echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		  echo "<a href='index.php'>Вернуться к списку категорий</a>";
		 echo "</div>";
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='edited_category')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 $id = $_POST['id'];
		 $caption = $_POST['caption'];
		 $text = $_POST['text'];
		 $image = $_POST['image'];
		 $date = $_POST['date'];
		 $category = $_GET['category'];
		 echo "<title>Категория $caption изменена</title>";
		 echo "<div id=content>";		 
		 $sql = "UPDATE $tableWebPage SET ParentId='root', Id='$id', Caption='$caption', ShortCaption='$caption', Intro='$text', Text='$text', Image='$image', ImageSmall='$image', Date='$date' WHERE Id='$category';";		 		 
		 $result = $conn->prepare($sql);		 
		 $result->execute();		 	 
		 $sql1 = "UPDATE $tableWebPage Set ParentId='$id' where ParentId='$category';";
		 $result2 = $conn->prepare($sql1);
		 $result2->execute();
		 if (!$result) echo "Не получилось изменить запись в $tableWebPage";
		 else 
		 {
			echo "Запись была успешно изменена!<br>";
			echo "<a href='index.php'>Вернуться к списку категорий</a>";
		 }
		 echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		 echo "<div id=content>";
		  echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		  echo "<a href='index.php'>Вернуться к списку категорий</a>";
		 echo "</div>";
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='edit_product')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 $sql2 = "SELECT * FROM $tableWebPage where Id='".$_GET['product']."'";
		 $res = $conn->prepare($sql2);
		 $res->execute();
		 $res->setFetchMode(PDO::FETCH_ASSOC);
		 $result = $res->fetchAll();
		 foreach ($result as $key=>$val)
		 {
		    $shortcaptionproduct = $val['ShortCaption'];
			$id = $val['Id'];
			$caption = $val['Caption'];
			$intro = $val['Intro'];
		    $text = $val['Text'];
		    $image = $val['Image'];
			$imagesmall = $val['ImageSmall'];
		    $date = $val['Date'];
		 }
		 echo "<title>Изменить продукт ".$shortcaptionproduct."</title>";
		 echo "<div id=navig>";
		  echo "<a href='index.php?show=".$_GET['category']."'>Вернуться назад</a>";
		 echo "</div>";
		 echo "<div id=content>";		  
		  $sql3 = "SELECT ShortCaption FROM $tableWebPage where Id='".$_GET['category']."'";
		  $res = $conn->prepare($sql3);
		  $res->execute();
		  $res->setFetchMode(PDO::FETCH_ASSOC);
		  $result = $res->fetchAll();
		  foreach ($result as $key=>$val)
		    $shortcaptioncategory = $val['ShortCaption'];
		  echo "<h2>Редактировать продукт ".$shortcaptionproduct." в категории ".$shortcaptioncategory."</h2>";
		  echo "<form action='index.php?show=edited_product&product=".$_GET['product']."&category=".$_GET['category']."' align='left' method=POST>";
		  echo "<table>";
		  echo "<tr><td>Id</td><td><p><input type='text' name='id' size='50' value='".$id."' /></p></td></tr>";
		  echo "<tr><td>Заголовок</td><td><p><input type='text' name='caption' size='50' value='".$caption."'/></p></td></tr>";
		  echo "<tr><td>Короткий Заголовок</td><td><p><input type='text' name='shortcaption' size='50' value='".$shortcaptionproduct."'/></p></td></tr>";
		  echo "<tr><td>Вступление</td><td><p><textarea name='intro' cols='40' rows='8'>".$intro."</textarea></p></td></tr>";
		  echo "<tr><td>Текст</td><td><p><textarea name='text' cols='40' rows='8'>".$text."</textarea></p></td></tr>";
		  echo "<tr><td>Картинка</td><td><p><input type='text' name='image' size='50' value='".$image."'/></p></td></tr>";
		  echo "<tr><td>Маленькая Картинка</td><td><p><input type='text' name='imagesmall' size='50' value='".$imagesmall."'/></p></td></tr>";
		  echo "<tr><td>Дата</td><td><p><input type='text' name='date' size='50' value='".$date."'/></p></td></tr>";
		  echo "</table>";
		  echo "<br><p align='center'><input type='submit' name='add' value='Сохранить' />";
		  echo "</form>";
		  echo "<p><a href='index.php?show=delete_product&product=".$_GET['product']."&category=".$_GET['category']."'>Удалить продукт</a></p>";
		 echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		echo "<div id=content>";
		 echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		 echo "<a href='index.php'>Вернуться к списку категорий</a>";
		echo "</div>";
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='edited_product')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 echo "<title>Продукт ".$_POST['shortcaption']." изменен</title>";
		 echo "<div id=content>";
		 $sql = "UPDATE $tableWebPage SET ParentId='".$_GET['category']."', Id='".$_POST['id'] . "', Caption='". $_POST['caption']."', ShortCaption='". $_POST['shortcaption']."', Intro='". $_POST['intro']."', Text='". $_POST['text']."', Image='". $_POST['image']."', ImageSmall='". $_POST['imagesmall']."', Date='". $_POST['date']."' WHERE Id='".$_GET['product']."';";
		 $result = $conn->prepare($sql);
		 $result->execute();
		 if (!$result) echo "Не получилось изменить запись в $tableWebPage";
		 else 
		 {
			echo "Запись была успешно изменена!<br>";
			echo "<a href='index.php?show=".$_GET['category']."'>Вернуться к списку продуктов</a>";
		 }
		 echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		 echo "<div id=content>";
		 echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		 echo "<a href='index.php'>Вернуться к списку категорий</a>";
		 echo "</div>";
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='delete_category')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 $sql2 = "Select ShortCaption from $tableWebPage where Id='".$_GET['category']."';";
		 $res = $conn->prepare($sql2);
		 $res->execute();
		 $res->setFetchMode(PDO::FETCH_ASSOC);
		 $result = $res->fetchAll();
		 foreach ($result as $key=>$val)
		   $result2 = $val['ShortCaption'];
		 echo "<title>Категория ".$result2." удалена</title>";
		 echo "<div id=content>";
		  $sql = "Delete from $tableWebPage where Id='".$_GET['category']."';";
		  $result = $conn->prepare($sql);
		  $result->execute();
		  $sql1 = "Delete from $tableWebPage where ParentId='".$_GET['category']."';";
		  $result1 = $conn->prepare($sql1);
		  $result1->execute();
		  if (!$result) echo "Невозможно удалить запись из таблицы $tableWebPage";
		  else 
		  {
			  echo "Категория ".$result2." успешно удалена!<br>";
			  echo "<a href='index.php'>Вернуться к списку категорий</a>";
		  }
	     echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		 echo "<div id=content>";
		  echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		  echo "<a href='index.php'>Вернуться к списку категорий</a>";
		 echo "</div>";
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='delete_product')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 $sql2 = "Select ShortCaption from $tableWebPage where Id='".$_GET['product']."';";
		 $res = $conn->prepare($sql2);
		 $res->execute();
		 $res->setFetchMode(PDO::FETCH_ASSOC);
		 $result = $res->fetchAll();
		 foreach ($result as $key=>$val)
		   $result2 = $val['ShortCaption'];
		 echo "<title>Продукт ".$result2." удален</title>";
		 echo "<div id=content>";
			 $sql = "Delete from $tableWebPage where Id='".$_GET['product']."';";
			 $result = $conn->prepare($sql);
			 $result->execute();
			 if (!$result) echo "Невозможно удалить запись из таблицы $tableWebPage";
			 else 
			 {
				 echo "Продукт ".$result2." успешно удален!<br>";
				 echo "<a href='index.php?show=".$_GET['category']."'>Вернуться к списку продуктов</a>";
			 }
		 echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		 echo "<div id=content>";
		  echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		  echo "<a href='index.php'>Вернуться к списку категорий</a>";
		 echo "</div>";
	 } 
}

if (isset($_GET['show'])&&$_GET['show']!=NULL&&$_GET['show']!='about'&&$_GET['show']!='add_category'&&$_GET['show']!='add_product'&&$_GET['show']!='added_category'&&$_GET['show']!='added_product'&&$_GET['show']!='edit_category'&&$_GET['show']!='edited_category'&&$_GET['show']!='edit_product'&&$_GET['show']!='edited_product'&&$_GET['show']!='delete_category'&&$_GET['show']!='delete_product'&&$_GET['show']!='login'&&$_GET['show']!='logged'&&$_GET['show']!='logout'&&$_GET['show']!='changepass'&&$_GET['show']!='changedpass'&&$_GET['show']!='info')
{
	$sql = "SELECT * FROM $tableWebPage where Id='".$_GET['show']."'";
	$res = $conn->prepare($sql);
	$res->execute();
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$result = $res->fetchAll();
	foreach ($result as $key=>$val)
	{
	  $parentid[$key] = $val['ParentId'];	  
	  $caption[$key] = $val['Caption'];
	  $infocategory = $val['Caption'];
	}
	echo "<title>Магазин компьютерной техники Корифей >> ".$caption[0]."</title>";
	$sql2 = "SELECT * FROM $tableWebPage where ParentId='".$_GET['show']."';";	
	$res = $conn->prepare($sql2);
	$res->execute();
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$q = $res->fetchAll();
	$n = count($q);	
	foreach ($q as $key=>$val)
		$infocategory = $val['Caption'];
	if ($n>0)
	{		  
		echo "<div id=path>";
		 echo "<a href='index.php'>Товары</a> >> ";
		 echo "<a href='index.php?show=".$_GET['show']."'>".$caption[0]."</a>";
		echo "</div>";
		if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
		{
			echo "<div id=content>";			
			echo "<h1>".$caption[0]."</h1>";
			echo "<table width=100% border=0 align=center>";
			foreach($q as $row)
			{				
				$obj1 = new Get_List($row);
				$obj1->printContent();				
			}
			echo "</table>";
			echo "<p><a href='index.php?show=info&category=".$_GET['show']."'>Справка</a></p>";
			echo "</div>";
			echo "<div id=navig>";
			echo "<table width=100% border=0 align=left>";
			foreach($q as $row)
			{				
				$obj1 = new Get_List($row);
				$obj1->printMenu();				
			}
			echo "</table>";
			echo "</div>";
		} 		
		if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
		{
			echo "<div id=navig>";
			 foreach($q as $row)
			 {				
				 $obj1 = new Get_List($row);
				 $obj1->printMenuAdmin();				
			 }  
			 echo "<a href='index.php?show=add_product&category=".$_GET['show']."'>Добавить продукт</a>";
			echo "</div>";
			echo "<div id=content>";
			 echo "<h1>".$caption[0]."</h1>";
			 echo "<table width=100% border=0 align=center>";
				 foreach($q as $row)
				 {				
					 $obj1 = new Get_List($row);
					 $obj1->printContentAdmin();				
				 } 
			 echo "</table>";
			 echo "<p><a href='index.php?show=info&category=".$_GET['show']."'>Справка</a></p>";			 
			echo "</div>";
		}
	} 
	if ($n==0)
	{	    
	    if ($parentid[$n]=='root')
		{
			echo "<div id=navig>";			 
			 echo "<a href='index.php'>Вернуться к списку категорий</a>";
			echo "</div>";
			echo "<div id=content>";
			 echo "<p>В данной категории еще нет продуктов</p>";
			 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)			 
				echo "<a href='index.php?show=add_product&category=".$_GET['show']."'>Добавить продукт</a>";	
			echo "<p><a href='index.php?show=info&category=".$_GET['show']."'>".$infocategory." Справка</a></p>";			
			echo "</div>";
		}
		if ($parentid[$n]!='root')
		{
			 $sql = "SELECT ShortCaption FROM $tableWebPage where Id='".$parentid[$n]."'";
			 $res = $conn->prepare($sql);
			 $res->execute();
			 $res->setFetchMode(PDO::FETCH_ASSOC);
			 $result1 = $res->fetchAll();
			 foreach ($result1 as $val)
			   $resultPrev2 = $val['ShortCaption'];
			 $sql = "SELECT * FROM $tableWebPage where ParentId='".$parentid[$n]."'";
			 $res = $conn->prepare($sql);
			 $res->execute();
			 $res->setFetchMode(PDO::FETCH_ASSOC);
			 $q = $res->fetchAll();			 			  
			 echo "<div id=path>";
			  echo "<a href='index.php'>Товары</a> >> ";
			  echo "<a href='index.php?show=".$parentid[$n]."'>".$resultPrev2."</a>";
			  echo " >> ".$caption[$n];    
			 echo "</div>";
			 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
			 {
				 echo "<div id=navig>"; 
				  echo "<table width=100% border=0 align=left>";
				  foreach($q as $row)
				  {					  
					   $obj1 = new Get_List($row);
					   $obj1->printMenu();					  
				  }
				  echo "</table>";
				 echo "</div>";
			 }
			 echo "<div id=content>";
			  echo "<table width=100% border=0 align=center>";  			   
			   foreach ($result as $row)
			   {
				   $obj1 = new Get_List($row);
				   $obj1->printLastContent();
			   }
			  echo "</table>";			 
			 echo "</div>";			 
			 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
			 {    
				 echo "<div id=navig>"; 				  
				  foreach($q as $row)
				  {
					   $obj1 = new Get_List($row);
					   $obj1->printMenuAdmin();					  
				  }
				 echo "</div>";  
			 }  
		}
	}
}

if (isset($_GET['show']) && $_GET['show']=='login')
{
	 echo "<title>Вход администратора</title>";
	 echo "<div id=content>";
	 echo "<form action=index.php?show=logged method=POST>";
	 echo "<p>Логин <input type=text name=login value=''></p>";
	 echo "<p>Пароль <input type=password name=password value=''></p>";
	 echo "<input type=submit value='Вход'>";
	 echo "</form>";
	 echo "</div>";
}

if (isset($_GET['show']) && $_GET['show']=='logged')
{		  			
	 echo "<div id=content>";	 	  
	  if ($_POST['login']!=$login or $_POST['password']!=$pass)
	  {		   
		   echo "<p>Неверно введены данные</p>";
		   echo "<p><a href='index.php?show=about'>Вернуться к странице Контакты</a></p>";  
	  }
	  if ($_POST['login']==$login&&$_POST['password']==$pass)
	  {		   
		   echo "<p>Вход администратора выполнен</p>"; 
		   $_SESSION['login']=$_POST['login'];
		   $_SESSION['password']=$_POST['password']; 
		   echo "<a href='index.php?show=about'>Перейти к странице Контакты</a>";
	  }
	 echo "</div>";
}

if (isset($_GET['show']) && $_GET['show']=='changepass')
{	 
	 if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	 {
		 echo "<title>Изменение пароля</title>";
		 echo "<div id=content>"; 
		  echo "<table align='center'><tr><td>Логин </td><td>".$_SESSION['login']."</td></tr>";
		   echo "<form action=index.php?show=changedpass method=POST>";
		    echo "<tr><td>Старый Пароль </td><td><input type=password name=password value=''></td></tr>";
		    echo "<tr><td>Новый Пароль </td><td><input type=password name=new_password value=''></td></tr>";
		    echo "<tr><td colspan=2><input type=submit value='Сохранить'></td></tr>";
		   echo "</form>";
		  echo "</table>";
		 echo "</div>";
	 }
	 if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	 {
		echo "<div id=content>";
		 echo "<p>У Вас нет прав для просмотра этой страницы.</p>";
		 echo "<a href='index.php'>Вернуться к списку категорий</a>";
		echo "</div>";
	 } 
}

if (isset($_GET['show']) && $_GET['show']=='changedpass')
{	 
	 if ($_SESSION['login']!=$login or $_POST['password']!=$pass)
	 {
		 echo "<title>Пароль не удалось изменить</title>";
		 echo "<div id=content>";  
		  echo "<p>Неверно введен старый пароль</p>";
		  echo "<p><a href='index.php?show=about'>Перейти к странице Контакты</a>";
		 echo "</div>";
	 }
	 if ($_SESSION['login']==$login&&$_POST['password']==$pass)
	 { 
		 echo "<title>Пароль изменен</title>";
		 echo "<div id=content>";  
		  $change = "UPDATE Users SET Password='".$_POST['new_password']."';";
		  $_SESSION['password']=$_POST['new_password'];
		  $result = $conn->prepare($change);
		  $result->execute();
		  echo "<p>Пароль успешно изменен!</p>";  
		  echo "<a href='index.php?show=about'>Перейти к странице Контакты</a>";  
		 echo "</div>";
	 }
 
}

if (isset($_GET['show']) && $_GET['show']=='logout')
{
	 echo "<div id=content>";
	  unset($_SESSION['login']);
	  unset($_SESSION['password']);
	  session_destroy();
	  echo "<p>Выход администратора выполнен</p>";
	  echo "<p><a href='index.php?show=about'>Перейти к странице Контакты</a></p>";
	 echo "</div>"; 
}

if (isset($_GET['show']) && $_GET['show']=='info')
{
	 $sql = "SELECT Header, Text from Information where id='".$_GET['category']."';";
	 $res = $conn->prepare($sql);
	 $res->execute();
	 $res->setFetchMode(PDO::FETCH_ASSOC);
	 $result = $res->fetchAll();
	 foreach ($result as $key=>$row)
	 {
		 $head = $row['Header'];
		 $text = $row['Text'];
	 }
	 echo "<div id=navig align='center'><a href='index.php'>К категориям</a></div>";   	 	 
	 echo "<title>Корифей >> Информация >> ".$head."</title>";  
	 echo "<div id=content>";
	  echo "<table align='left'>";
	   echo "<tr><td><h1>".$head."</h1></td></tr>";
	   echo "<tr><td align='left'>".$text."</td></tr>";
	  echo "</table>";	  
	 echo "</div>";
}

if (isset($_GET['show']) && $_GET['show']=='about')
{
	 echo "<title>Магазин компьютерной техники Корифей</title>";
	 echo "<div id='content'>";
	  echo "<h1><strong>Магазин компьютерной техники Корифей</strong></h1>";
	  echo "<p><b>Компьютерная техника</b> от лучших мировых производителей для дома и офиса</p>";
	  echo "<p>Наш адресс: г.Киев, улица Хмельницкая, 10</p>";
	  echo "<p>Телефон: 8(044) 492-73-63</p>";	  
	 echo "</div>"; 
	 echo "<div id='navig'>";	  
	  if ($_SESSION['login']!=$login or $_SESSION['password']!=$pass)
	  {
		  echo "<p align='center'><a href='index.php'>Компьютерная техника</a>";
		  echo "<p align='center'><a href='index.php?show=login'>Вход администратора</a></p>";  
	  }
	  if ($_SESSION['login']==$login&&$_SESSION['password']==$pass)
	  {
		  echo "<p align='center'><a href='index.php'>Компьютерная техника</a></p>";
		  echo "<p align='center'><a href='index.php?show=changepass'>Изменить пароль</a></p>";
		  echo "<p align='center'><a href='index.php?show=logout'>Выход</a></p>";
	  }
	 echo "</div>";
}
?>
</body>
</html>