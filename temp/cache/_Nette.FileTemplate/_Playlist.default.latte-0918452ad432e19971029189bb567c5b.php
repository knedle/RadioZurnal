<?php //netteCache[01]000382a:2:{s:4:"time";s:21:"0.19119900 1330615288";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:60:"C:\wamp\www\radiozurnal\app\templates\Playlist\default.latte";i:2;i:1330615283;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"52832ac released on 2012-02-24";}}}?><?php

// source file: C:\wamp\www\radiozurnal\app\templates\Playlist\default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'z352atp1yj')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb78473b343b_content')) { function _lb78473b343b_content($_l, $_args) { extract($_args)
;Nette\Latte\Macros\FormMacros::renderFormBegin($form = $_form = $_control["searchForm"], array('class' => "ac ajaxSubmit")) ?>
    <?php echo $_form["keyword"]->getControl()->addAttributes(array()) ?>

    <?php echo $_form["find"]->getControl()->addAttributes(array()) ?>

<?php Nette\Latte\Macros\FormMacros::renderFormEnd($_form) ?>



<div id="<?php echo $_control->getSnippetId('list') ?>"><?php call_user_func(reset($_l->blocks['_list']), $_l, $template->getParameters()) ?>
</div>
<div id="<?php echo $_control->getSnippetId('addForm') ?>"><?php call_user_func(reset($_l->blocks['_addForm']), $_l, $template->getParameters()) ?>
</div>
<h2>Zbývá identifikovat <?php echo($finalCount-count($interpretSongs)) ?> skladeb</h2>

<p> 
    V článku Lidových novin z 9. ledna 2012
    <a href="http://www.lidovky.cz/vysilame-malo-skladeb-priznal-sef-ceskeho-rozhlasu-fxz-/ln-media.asp?c=A120108_153143_ln-media_rka">Vysíláme málo skladeb, přiznal šéf Českého rozhlasu</a>
    bylo generálním ředitelem Českého rozhlasu Peterem Duhanem uvedeno, že playlist Radiožurnálu má okolo 890 skladeb.
    <br />
    <b>edit z twitteru na můj dotaz</b> 
    @rozanek: <i>To je proces na delší dobu, ale v lednu jich bylo kolem 950, těch 890 byl prosinec. Nic bližšího nevím.</i>
    <br />
    <b>Zkusme je vyjmenovat!</b>
</p>

<p>
    Jak získávat skladby? Já používám pro identifikaci svům mobil a v něm aplikaci SoundHound, která dokáže identifikovat skladbu "z poslechu".
    Pokud svou skladbu v listu nenajdete (použijte vyhledávání), můžete ji pomocí zobrazeného formuláře přidat. 
    Pokud ji najdete, potvrďte její hranost tlačítkem "právě hraje", je-li k dispozici...
</p>

<?php
}}

//
// block _list
//
if (!function_exists($_l->blocks['_list'][] = '_lb00de0f32f3__list')) { function _lb00de0f32f3__list($_l, $_args) { extract($_args); $_control->validateControl('list')
?>
<table class="table table-striped table-bordered table-condensed">
    <thead>
        <tr>
            <th>interpret</th>
            <th>song</th>
            <th>...</th>
            <th>info</th>
        </tr>
    </thead>

<?php $pocet = count($interpretSongs) ?>

<?php if ($interpretSongs): ?>    <tbody>
<?php $iterations = 0; foreach ($interpretSongs->limit(25) as $interpretSong): ?>        <tr>
            <td><?php echo Nette\Templating\Helpers::escapeHtml($interpretSong->interpret->name, ENT_NOQUOTES) ?></td>
            <td><?php echo Nette\Templating\Helpers::escapeHtml($interpretSong->song->title, ENT_NOQUOTES) ?></td>
            <td>
<?php if ((date('Y-m-d', strtotime($interpretSong->created_at)) == date('Y-m-d'))): ?>
                <a class="label label-success">Dnes přidána</a>
<?php elseif ((date('Y-m-d', strtotime($interpretSong->modified_at)) == date('Y-m-d'))): ?>
                <a class="label label-info">Dnes hrála</a>
<?php else: ?>
                <a class="btn ajax" href="<?php echo htmlSpecialChars($_control->link("playNow!", array($interpretSong->interpret_id.'-'.$interpretSong->song_id))) ?>
">Právě hraje</a>
<?php endif ;if ((false)): ?>
                <a class="btn ajax" href="<?php echo htmlSpecialChars($_control->link("delete!", array($interpretSong->interpret_id.'-'.$interpretSong->song_id))) ?>
">Smazat</a>
<?php endif ?>
            </td>
            <td>
                <a href="http://www.youtube.com/results?search_query=<?php echo htmlSpecialChars($interpretSong->interpret->name) ?>
 <?php echo htmlSpecialChars($interpretSong->song->title) ?>">hledej na Youtube</a>
            </td>
        </tr>
<?php $iterations++; endforeach ?>
    </tbody>    
<?php endif ?>

    <tfoot>
        <tr>
            <td colspan="4">Odpovídá filtraci: <?php echo Nette\Templating\Helpers::escapeHtml($pocet, ENT_NOQUOTES) ?>
 / Zobrazeno: <?php echo Nette\Templating\Helpers::escapeHtml($iterations, ENT_NOQUOTES) ?></td>
        </tr>
    </tfoot>

</table>
<?php
}}

//
// block _addForm
//
if (!function_exists($_l->blocks['_addForm'][] = '_lb6a445b810b__addForm')) { function _lb6a445b810b__addForm($_l, $_args) { extract($_args); $_control->validateControl('addForm')
;if (!count($interpretSongs)): Nette\Latte\Macros\FormMacros::renderFormBegin($form = $_form = $_control["songSaveForm"], array('class' => "ac")) ?>
            <?php echo $_form["interpret"]->getControl()->addAttributes(array()) ?>

            <?php echo $_form["song"]->getControl()->addAttributes(array()) ?>

            <?php echo $_form["save"]->getControl()->addAttributes(array()) ?>

            <?php echo $_form["cancel"]->getControl()->addAttributes(array()) ?>

<?php Nette\Latte\Macros\FormMacros::renderFormEnd($_form) ;endif ;
}}

//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lb276d64d0b2_head')) { function _lb276d64d0b2_head($_l, $_args) { extract($_args)
?><style>

</style>
<script>
    $(document).ready(function(){
        
        setInterval(function(){
            $( ".addInterpret" ).autocomplete({
                    source: <?php echo Nette\Templating\Helpers::escapeJs($_control->link("autocompleteInterpret!")) ?> ,
                    minLength: 2,
                    select: function( event, ui ) { }
            });        

            $( ".addSong" ).autocomplete({
                    source: <?php echo Nette\Templating\Helpers::escapeJs($_control->link("autocompleteSong!")) ?> ,
                    minLength: 2,
                    select: function( event, ui ) { }
            });  
        }, 500);        
                
        $('input[name=keyword]').keyup(function(){
           if ($(this).val().length > 2) {
               $(this).closest('form').submit();
           }
        });
        
        
        $("form.ajaxSubmit").submit(function () {
                $(this).ajaxSubmit();
                return false;
        });           
                
    });
</script>
<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = empty($template->_extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars())  ?>


<?php call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars()) ; 