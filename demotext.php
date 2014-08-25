<?php /* @var $this de\flatplane\interfaces\documentElements\TextInterface */ ?>
This is sample content including a complicated Footnote<?=$this->addFootnote('really compilcated with <b>bold</b> text and <a href="google.com" style="text-decoration:none; color: #000000">links</a> and even some ‹br›\'s <br>here');?> and so on.<br>
<br>
LOREM IPSUM DOLOR SIT AMET<?=$this->addFootnote('somewhat easier footnote');?>
