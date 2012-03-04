<?php //netteCache[01]000394a:2:{s:4:"time";s:21:"0.50373400 1330802902";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:72:"C:\wamp\www\radiozurnal\libs\Nette.Addons\VisualPaginator\template.phtml";i:2;i:1330802888;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"52832ac released on 2012-02-24";}}}?><?php

// source file: C:\wamp\www\radiozurnal\libs\Nette.Addons\VisualPaginator\template.phtml

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'lrucslv9ym')
;
// prolog Nette\Latte\Macros\UIMacros

// snippets support
if (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
if ($paginator->pageCount > 1): ?>
<div class="pagination pagination-centered">
    <ul>
<?php if ($paginator->isFirst()): ?>
	<li class="disabled"><a>« Previous</a></li>
<?php else: ?>
	<li><a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $paginator->page - 1))) ?>" rel="prev">« Previous</a><li>
<?php endif ?>

<?php $iterations = 0; foreach ($iterator = $_l->its[] = new Nette\Iterators\CachingIterator($steps) as $step): if ($step == $paginator->page): ?>
		<li class="active"><a href="#"><?php echo Nette\Templating\Helpers::escapeHtml($step, ENT_NOQUOTES) ?></a></li>
<?php else: ?>
		<li><a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $step))) ?>
"><?php echo Nette\Templating\Helpers::escapeHtml($step, ENT_NOQUOTES) ?></a></li>
<?php endif ?>
	<?php if ($iterator->nextValue > $step + 1): ?><li class="disabled"><a>…</a></li><?php endif ?>

<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>

<?php if ($paginator->isLast()): ?>
            <li class="disabled"><a >Next »</a></li>
<?php else: ?>
            <li><a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $paginator->page + 1))) ?>" rel="next">Next »</a></li>
<?php endif ?>
    </ul>        
</div>
<?php endif ;