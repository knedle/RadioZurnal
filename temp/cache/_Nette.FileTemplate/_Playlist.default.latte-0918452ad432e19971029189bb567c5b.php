<?php //netteCache[01]000382a:2:{s:4:"time";s:21:"0.83411100 1330895586";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:60:"C:\wamp\www\radiozurnal\app\templates\Playlist\default.latte";i:2;i:1330895584;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"52832ac released on 2012-02-24";}}}?><?php

// source file: C:\wamp\www\radiozurnal\app\templates\Playlist\default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'yjglq3ae2n')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbd60b82e8e4_content')) { function _lbd60b82e8e4_content($_l, $_args) { extract($_args)
;Nette\Latte\Macros\FormMacros::renderFormBegin($form = $_form = $_control["searchForm"], array('class' => "ac ajaxSubmit")) ?>
        <?php echo $_form["keyword"]->getControl()->addAttributes(array()) ?>

        <?php echo $_form["find"]->getControl()->addAttributes(array()) ?>

<span id="<?php echo $_control->getSnippetId('addButoon') ?>"><?php call_user_func(reset($_l->blocks['_addButoon']), $_l, $template->getParameters()) ?>
</span><?php Nette\Latte\Macros\FormMacros::renderFormEnd($_form) ?>

<hr />



<div id="<?php echo $_control->getSnippetId('list') ?>"><?php call_user_func(reset($_l->blocks['_list']), $_l, $template->getParameters()) ?>
</div>
<div id="<?php echo $_control->getSnippetId('addForm') ?>"><?php call_user_func(reset($_l->blocks['_addForm']), $_l, $template->getParameters()) ?>
</div>
<div class="hero-unit">
    <h2 class="ac">Zbývá identifikovat ~<?php echo($finalCount-$totalCount) ?> skladeb</h2>


<p> 
    V článku Lidových novin z 9. ledna 2012
    <a href="http://www.lidovky.cz/vysilame-malo-skladeb-priznal-sef-ceskeho-rozhlasu-fxz-/ln-media.asp?c=A120108_153143_ln-media_rka">Vysíláme málo skladeb, přiznal šéf Českého rozhlasu</a>
    bylo generálním ředitelem Českého rozhlasu Peterem Duhanem uvedeno, že playlist Radiožurnálu má okolo 890 skladeb.
</p>

<p>
    A novější informace - odpověd na můj dotaz na twitteru:<br />
    <a href="https://twitter.com/#!/3knedle/status/175238636458094592">@rozanek</a>: <i>To je proces na delší dobu, ale v lednu jich bylo kolem 950, těch 890 byl prosinec. Nic bližšího nevím.</i>
</p>    


<!--h2 class="ac">Zkusme je vyjmenovat!</h2-->

</div>

<div class="well">
    <p>
        <b>Jak získávat skladby? </b><br />
        Já používám pro identifikaci svům mobil (Android) a v něm aplikace <a href="http://www.soundhound.com/">SoundHound</a> nebo <a href="http://www.shazam.com/">Shazam</a>, která dokáží identifikovat skladbu "z poslechu".<br />
        Shazam je obecně rychlejší a úspěšněji identifikuje CZ/SK skladby; s těmi má SoundHound problémy. Ovšem SoundHound se mi líbí více, takže to střídám...        
    </p>
    <p>
        Pokud svou skladbu v seznamu nenajdete (použijte vyhledávání), můžete ji pomocí zobrazeného formuláře přidat. <br />
        Pokud ji najdete, potvrďte její hranost přes ikonku <a class="btn"><i class="icon-headphones"></i></a>, je-li k dispozici...
    </p>
</div>

<?php
}}

//
// block _addButoon
//
if (!function_exists($_l->blocks['_addButoon'][] = '_lb479d3b7d07__addButoon')) { function _lb479d3b7d07__addButoon($_l, $_args) { extract($_args); $_control->validateControl('addButoon')
?><a class="btn ajax" href="<?php echo htmlSpecialChars($_control->link("addNew!")) ?>
"><i class="icon-plus-sign"></i> Add</a>
<?php
}}

//
// block _list
//
if (!function_exists($_l->blocks['_list'][] = '_lb332ca9b4f6__list')) { function _lb332ca9b4f6__list($_l, $_args) { extract($_args); $_control->validateControl('list')
?>
<table class="table table-striped table-bordered table-condensed">
    <thead>
        <tr>
            <th>interpret</th>
            <th>song</th>
            <th class="ac">info</th>
            <th class="ac">více...</th>
        </tr>
    </thead>

<?php $pocet = count($interpretSongs) ?>

<?php if ($interpretSongs): ?>    <tbody>
<?php $iterations = 0; foreach ($interpretSongs as $interpretSong): ?>        <tr>
            <td><?php echo Nette\Templating\Helpers::escapeHtml($interpretSong->interpret->name, ENT_NOQUOTES) ?></td>
            <td><?php echo Nette\Templating\Helpers::escapeHtml($interpretSong->song->title, ENT_NOQUOTES) ?></td>
            <td class="ac">
<?php if ((date('Y-m-d', strtotime($interpretSong->created_at)) == date('Y-m-d'))): ?>
                <span class="label label-success">Dnes přidána</span>
<?php elseif ((date('Y-m-d', strtotime($interpretSong->modified_at)) == date('Y-m-d'))): ?>
                <span class="label label-info">Dnes hrála</span>
<?php else: if (!empty($confirm)): ?>
                <a class="btn ajax addConfirm" rel="tooltip" title="Právě hraje" href="<?php echo htmlSpecialChars($_control->link("playNow!", array($interpretSong->interpret_id.'-'.$interpretSong->song_id, 1))) ?>
"><i class="icon-headphones"></i></a>
<?php else: ?>
                <a class="btn ajax addConfirm" rel="tooltip" title="Právě hraje" href="<?php echo htmlSpecialChars($_control->link("playNow!", array($interpretSong->interpret_id.'-'.$interpretSong->song_id))) ?>
"><i class="icon-headphones"></i></a>
<?php endif ?>
                <span class="label" rel="tooltip" title="poslední den, kdy byla slyšena"><?php echo Nette\Templating\Helpers::escapeHtml((strtotime($interpretSong->created_at) < strtotime($interpretSong->modified_at)? (date('d.m.Y', strtotime($interpretSong->modified_at))) : (date('d.m.Y', strtotime($interpretSong->created_at)))), ENT_NOQUOTES) ?></span>
<?php endif ;if ((false)): ?>
                <a class="btn ajax" href="<?php echo htmlSpecialChars($_control->link("delete!", array($interpretSong->interpret_id.'-'.$interpretSong->song_id))) ?>
">Smazat</a>
<?php endif ?>
            </td>
            <td class="ac">
                <a rel="tooltip" title="hledej na youtube" href="http://www.youtube.com/results?search_query=<?php echo htmlSpecialChars($interpretSong->interpret->name) ?>
 <?php echo htmlSpecialChars($interpretSong->song->title) ?>"><img src="http://s.ytimg.com/yt/img/creators_corner/YouTube/youtube_24x24.png" alt="Přihlaste se k odběru mého kanálu na YouTube" /></a>
            </td>
        </tr>
<?php $iterations++; endforeach ?>
    </tbody>    
<?php endif ?>

    <tfoot>
<?php if (count($iterations)): ?>
        <tr>        

            <td colspan="4">
                Odpovídá filtraci: <?php echo Nette\Templating\Helpers::escapeHtml($totalCount, ENT_NOQUOTES) ?>
 / Zobrazeno: <?php echo Nette\Templating\Helpers::escapeHtml($iterations, ENT_NOQUOTES) ?>

            </td>
        </tr>
<?php endif ?>
        <tr>    
            <td colspan="4" class="ac">
<?php if (count($iterations)): $_ctrl = $_control->getComponent("vp"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ;else: ?>
                <b>Na základě filtrace nebyla nalezena žádná data...</b>
<?php endif ?>
            </td>
        </tr>
    </tfoot>

</table>
<?php
}}

//
// block _addForm
//
if (!function_exists($_l->blocks['_addForm'][] = '_lb0ca0a6b83e__addForm')) { function _lb0ca0a6b83e__addForm($_l, $_args) { extract($_args); $_control->validateControl('addForm')
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
if (!function_exists($_l->blocks['head'][] = '_lbbb583e2274_head')) { function _lbbb583e2274_head($_l, $_args) { extract($_args)
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
                                       
        $('table').tooltip({
            selector: "[rel=tooltip]",
            placement: "left"
        });
        
        $('.addConfirm').each(function(){
            $(this).attr("href", $(this).attr('href') + "&confirm=1");
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