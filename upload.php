<META content="text/html; charset =iso-8859-1" http-equiv=Content-Type>
<?
$maxsize = 127000000;
$directory = $_POST['directory'];
$filename = $_FILES['upload']['name'];
$filename = strtolower(str_replace(array("'",'"',"?","!","@","#","$","%","^","&","*","(",")","+","+"),array('-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-'),$filename));
$filename= strtr($filename,"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
$filesize = $_FILES['upload']['size'];
$filetype = $_FILES['upload']['type']; 
if ($filesize > 0)
{
if ($_FILES['upload']['error'] > 0) {$erreur = "Erreur lors du transfert";}
if ($_FILES['upload']['size'] > $maxsize) {$erreur = "Le fichier est trop gros";}
$image_sizes = getimagesize($_FILES['upload']['tmp_name']);
if ($directory != "")
{
$filename = $directory . "/" . $filename;
}
$resultat = move_uploaded_file($_FILES['upload']['tmp_name'],$filename);
?>
 <script type="text/javascript">
   alert("Le fichier <?php echo $filename;?> a été ajouté à votre espace de stockage privé.");
   <?
   if ($directory != "")
{
?>
   window.location = "index.php?dir=<?php echo $directory;?>"
   <?
} else {
?>
window.location = "index.php"
<?
}
?>
 </script>
 <?
}
?>