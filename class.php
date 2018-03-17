<?php
class Webpage
{
	 public $ParentId;
	 public $Id;
	 public $Caption;
	 public $ShortCaption;
	 public $Intro;
	 public $Text;
	 public $Image;
	 public $ImageSmall;
	 public $Date;

	 function __construct($ParentId,$Id,$Caption,$ShortCaption,$Intro,$Text,$Image,$ImageSmall,$Date)
	 {
		$this->ParentId=$ParentId;
		$this->Id=$Id;
		$this->Caption=$Caption;
		$this->ShortCaption=$ShortCaption;
		$this->Intro=$Intro;
		$this->Text=$Text;
		$this->Image=$Image;
		$this->ImageSmall=$ImageSmall;
		$this->Date=$Date;
	 }
	 function __destruct() {}
	 function printCategoryAdmin()
	 {
		echo "<tr><td colspan=2><h2>".$this->Caption."</h2></td></tr>";
		echo "<tr><td colspan=2>";
		echo "<a href='index.php?show=edit_category&category=".$this->Id."'>Редактировать категорию</a>";
		echo "</td></tr>";
		echo "<tr><td><img alt='".$this->Caption."' src='images/".$this->ImageSmall."'></td>";
		echo "<td align='left'>".$this->Intro."</td></tr>";
		echo "<tr><td colspan=2 align='right'>".$this->Date."</td></tr>";
	 }
	 function printCategory()
	 {
		echo "<tr><td colspan=2><h2><a href='index.php?show=".$this->Id."'>".$this->Caption."</a></h2></td></tr>";
		echo "<tr><td><img alt='".$this->Caption."' src='images/".$this->ImageSmall."'></td>";
		echo "<td align='left'>".$this->Intro."</td></tr>";
		echo "<tr><td colspan=2 align='right'>".$this->Date."</td></tr>";
	 }
	 function printContent()
	 {
		echo "<tr><td colspan=2><h2><a href='index.php?show=".$this->Id."'>".$this->ShortCaption."</a></h2></td></tr>";
		echo "<tr><td><img alt='".$this->Caption."' src='images/".$this->ImageSmall."'></td>";
		echo "<td align='left'>".$this->Intro."</td></tr>";
		echo "<tr><td colspan=2 align='right'>".$this->Date."</td></tr>";
	 }
	 function printContentAdmin()
	 {
		echo "<tr><td colspan=2><h2>".$this->ShortCaption."</h2></td></tr>";
		echo "<tr><td colspan=2><form action='index.php?show=edit_product&product=".$this->Id."&category=".$this->ParentId."' method=POST>";
		echo "<a href='index.php?show=edit_product&product=".$this->Id."&category=".$this->ParentId."'>Редактировать продукт</a>";
		echo "</td></tr>";
		echo "<tr><td><img alt='".$this->Caption."' src='images/".$this->ImageSmall."'></td>";
		echo "<td align='left'>".$this->Intro."</td></tr>";
		echo "<tr><td colspan=2 align='right'>".$this->Date."</td></tr>";
	 }
	 function printLastContent()
	 {
		 echo "<tr><td><h1>".$this->Caption."</h1></td></tr>";
		 echo "<tr><td><img alt='".$this->Caption."' src='images/".$this->Image."'></td></tr>";
		 echo "<tr><td align='left'>".$this->Text."</td></tr>";
		 echo "<tr><td align='right'>".$this->Date."</td></tr>";
	 }
	 function printMenu()
	 {
		echo "<p><a href='index.php?show=".$this->Id."'>".$this->ShortCaption."</a></p>";
	 }
	 function printMenuAdmin()
	 {
		echo "<p><a href='index.php?show=".$this->Id."'>".$this->ShortCaption."</a></p>";
	}
}
class Get_List extends Webpage
{
	function __construct($row) 
	{
		parent::__construct($row['ParentId'],$row['Id'],$row['Caption'],$row['ShortCaption'],$row['Intro'],$row['Text'],$row['Image'],$row['ImageSmall'],$row['Date']);
	}
}

?>