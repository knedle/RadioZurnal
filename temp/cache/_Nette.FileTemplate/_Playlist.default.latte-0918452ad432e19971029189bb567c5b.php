<?php //netteCache[01]000382a:2:{s:4:"time";s:21:"0.99702300 1331846904";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:60:"C:\wamp\www\radiozurnal\app\templates\Playlist\default.latte";i:2;i:1331846901;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"52832ac released on 2012-02-24";}}}?><?php

// source file: C:\wamp\www\radiozurnal\app\templates\Playlist\default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'r7nmrnhywb')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb0221f41d54_content')) { function _lb0221f41d54_content($_l, $_args) { extract($_args)
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

<div id="disqus_thread"></div>
<script type="text/javascript">
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    var disqus_shortname = 'radiozurnal3teckycz'; // required: replace example with your forum shortname

    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>

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

<div class="modal hide fade" id="myModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Editace hodnoty</h3>
    </div>
    <div class="modal-body">
<?php $_ctrl = $_control->getComponent("modalEditForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
    </div>
    <div class="modal-footer">
<!--        <a href="#" class="btn btn-primary">Save changes</a>
        <a href="#" class="btn">Close</a>-->
    </div>
</div>

<?php
}}

//
// block _addButoon
//
if (!function_exists($_l->blocks['_addButoon'][] = '_lbfcb98a92b0__addButoon')) { function _lbfcb98a92b0__addButoon($_l, $_args) { extract($_args); $_control->validateControl('addButoon')
?><a class="btn ajax" href="<?php echo htmlSpecialChars($_control->link("addNew!")) ?>
"><i class="icon-plus-sign"></i> Add</a>
<?php
}}

//
// block _list
//
if (!function_exists($_l->blocks['_list'][] = '_lb841c369af0__list')) { function _lb841c369af0__list($_l, $_args) { extract($_args); $_control->validateControl('list')
?>
<table class="table table-striped table-bordered table-condensed">
    <thead>
        <tr style="background-color: #ddd">
            <th>
                interpret
<?php if ($showSort && ($sortBy != 'interpret-name-asc')): if (preg_match('/interpret-name/', $sortBy)): ?>
                <i class="icon-chevron-down icon-gray"></i>
<?php endif ?>
                <a class="xajax" href="<?php echo htmlSpecialChars($_control->link("sort!", array('name'))) ?>
"><i class="icon-chevron-up"></i></a>
<?php elseif ($showSort): ?>
                <a class="xajax" href="<?php echo htmlSpecialChars($_control->link("sort!", array('name', 'desc'))) ?>
"><i class="icon-chevron-down"></i></a>
<?php if (preg_match('/interpret-name/', $sortBy)): ?>
                <i class="icon-chevron-up icon-gray"></i>
<?php endif ;endif ?>
            </th>
            <th>
                song
<?php if ($showSort && ($sortBy != 'song-title-asc')): if (preg_match('/song-title/', $sortBy)): ?>
                <i class="icon-chevron-down icon-gray" title="seřazeno seestupně"></i>
<?php endif ?>
                <a class="xajax" title="seřadit vzestupně" href="<?php echo htmlSpecialChars($_control->link("sort!", array('title'))) ?>
"><i class="icon-chevron-up"></i></a>
<?php elseif ($showSort): ?>
                <a class="xajax" title="seřadit sestupně" href="<?php echo htmlSpecialChars($_control->link("sort!", array('title', 'desc'))) ?>
"><i class="icon-chevron-down"></i></a>
<?php if (preg_match('/song-title/', $sortBy)): ?>
                <i class="icon-chevron-up icon-gray" title="seřazeno vzestupně"></i>
<?php endif ;endif ?>
            </th>
            <th class="ac span1">
                rok
<?php if ($showSort && ($sortBy != 'year-asc')): if (preg_match('/year/', $sortBy)): ?>
                <i class="icon-chevron-down icon-gray"></i>
<?php endif ?>
                <a class="xajax" href="<?php echo htmlSpecialChars($_control->link("sort!", array('year'))) ?>
"><i class="icon-chevron-up"></i></a>
<?php elseif ($showSort): ?>
                <a class="xajax" href="<?php echo htmlSpecialChars($_control->link("sort!", array('year', 'desc'))) ?>
"><i class="icon-chevron-down"></i></a>
<?php if (preg_match('/year/', $sortBy)): ?>
                <i class="icon-chevron-up icon-gray"></i>
<?php endif ;endif ?>
            </th>
            <th class="ac span2">
                info
<?php if ($showSort && ($sortBy != 'created-at-asc')): if (preg_match('/created-at/', $sortBy)): ?>
                <i class="icon-chevron-down icon-gray"></i>
<?php endif ?>
                <a class="xajax" href="<?php echo htmlSpecialChars($_control->link("sort!", array('time'))) ?>
"><i class="icon-chevron-up"></i></a>
<?php elseif ($showSort): ?>
                <a class="xajax" href="<?php echo htmlSpecialChars($_control->link("sort!", array('time', 'desc'))) ?>
"><i class="icon-chevron-down"></i></a>
<?php if (preg_match('/created-at/', $sortBy)): ?>
                <i class="icon-chevron-up icon-gray"></i>
<?php endif ;endif ?>
            </th>
            <th class="ac">více...</th>
        </tr>
    </thead>
    
<?php $pocet = count($interpretSongs) ?>

<?php if ($interpretSongs): ?>    <tbody>
<?php $iterations = 0; foreach ($interpretSongs as $interpretSong): ?>        <tr data-id='<?php echo json_encode(array('song_id'=>$interpretSong->song_id,'interpret_id'=>$interpretSong->interpret_id)) ?>' data-table="interpret_song">
            <td data-id="<?php echo htmlSpecialChars($interpretSong->interpret->id) ?>
" data-column="name" data-table="interpret"><?php echo Nette\Templating\Helpers::escapeHtml($interpretSong->interpret->name, ENT_NOQUOTES) ?></td>
            <td data-id="<?php echo htmlSpecialChars($interpretSong->song->id) ?>
" data-column="title" data-table="song"><?php echo Nette\Templating\Helpers::escapeHtml($interpretSong->song->title, ENT_NOQUOTES) ?></td>
            <td class="ac" data-column="year"><?php echo Nette\Templating\Helpers::escapeHtml($interpretSong->year == "0000" ? "" : $interpretSong->year, ENT_NOQUOTES) ?></td>
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
 <?php echo htmlSpecialChars($interpretSong->song->title) ?>"><img src="<?php echo htmlSpecialChars($basePath) ?>/images/youtube-24x24.png" alt="hledej na youtube" /></a>
                <a rel="tooltip" title="hledej na wikipedii" href="http://www.google.cz/search?q=wikipedia <?php echo htmlSpecialChars($interpretSong->interpret->name) ?>
 <?php echo htmlSpecialChars($interpretSong->song->title) ?>"><img src="<?php echo htmlSpecialChars($basePath) ?>/images/wikipedia-24x24.png" alt="hledej na wikipedii" /></a>
            </td>
        </tr>
<?php $iterations++; endforeach ?>
    </tbody>    
<?php endif ?>

    <tfoot>
<?php if (count($iterations)): ?>
        <tr>        

            <td colspan="5">
                Odpovídá filtraci: <?php echo Nette\Templating\Helpers::escapeHtml($totalCount, ENT_NOQUOTES) ?>
 / Zobrazeno: <?php echo Nette\Templating\Helpers::escapeHtml($iterations, ENT_NOQUOTES) ?>

            </td>
        </tr>
<?php endif ?>
        <tr>    
            <td colspan="5" class="ac">
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
if (!function_exists($_l->blocks['_addForm'][] = '_lbd1f65d98f5__addForm')) { function _lbd1f65d98f5__addForm($_l, $_args) { extract($_args); $_control->validateControl('addForm')
;if ((!count($interpretSongs) || !empty($showAddForm) )): Nette\Latte\Macros\FormMacros::renderFormBegin($form = $_form = $_control["songSaveForm"], array('class' => "ac")) ?>
            <?php echo $_form["interpret"]->getControl()->addAttributes(array()) ?>

            <?php echo $_form["song"]->getControl()->addAttributes(array()) ?>

            <?php echo $_form["year"]->getControl()->addAttributes(array()) ?>

            <?php echo $_form["save"]->getControl()->addAttributes(array()) ?>

            <?php echo $_form["cancel"]->getControl()->addAttributes(array()) ?>

<?php Nette\Latte\Macros\FormMacros::renderFormEnd($_form) ;endif ;
}}

//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lb669fecf82b_head')) { function _lb669fecf82b_head($_l, $_args) { extract($_args)
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
          
        /*  
        $('input[name=keyword]').keyup(function(){
           if ($(this).val().length > 2) {
               $(this).closest('form').submit();
           }
        });
        */
        
        
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
                
        // zobrazeni editacniho okenka
        $('[data-column]').live('dblclick', function(){            
            var table = $(this).attr('data-table');
            var column = $(this).attr('data-column'); 
            var primaryKey = $(this).attr('data-id');
            if (!table) {
                table = $(this).closest('[data-table]').attr('data-table');
            }
            if (!column) {
                column = $(this).closest('[data-column]').attr('data-column');
            }
            if (!primaryKey) {
                primaryKey = $(this).closest('[data-id]').attr('data-id');
            }
            
            $.ajax({
                url: <?php echo Nette\Templating\Helpers::escapeJs($_control->link("modalEditForm!")) ?>,
                data: {
                    table: table,
                    column: column,
                    primaryKey: primaryKey
                },
                dataType: "json",
                success: function(data){                   
                    if (data) {
                        var modal = $('#myModal');
                        var input = null;
                        $('[name=primaryKey]', $(modal)).val(data.primaryKey);
                        $('[name=table]', $(modal)).val(data.table);
                        $('[name=column]', $(modal)).val(data.column);
                        // projet selectOne a smazat ty co nejsou shodneho typu
                        $('.selectOne', $(modal)).val(data.value).each(function(){
                            if ($(this).attr('data-type') == data.type) {
                               $(this).removeAttr('disabled').show();
                               input = this;
                            }
                            else {
                                $(this).attr('disabled', 'disabled').hide();
                            }
                        });                    
                        $('#myModal').modal('toggle');


                    }
                }
            });

        });
        
        $('#myModal').on('shown', function () {
            $('.selectOne:first:visible', $(this)).focus().select();
        });
        
        jwerty.key('ctrl+i', function () {
            console.log('jump to interpret add input');
            $('.addInterpret').focus();
        });

        jwerty.key('ctrl+alt+i', function () {
            console.log('copy first interpret name to interpret add input interpret and jump to song add input');
            var interpret = $('[data-table=interpret]:first').text();
            console.log(interpret);
            $('.addInterpret').val(interpret);
            $('.addSong').focus();
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