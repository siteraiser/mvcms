<?php
//    <p><strong><a href="https://github.com/dbushell/Nestable">Code on GitHub</a></strong></p>

   

//$phpArray=json_decode('[{"name":"Item 1","id":1},{"name":"Item 2","id":2,"children":[{"name":"Item 3","id":3},{"name":"Item 4","id":4,"children":[{"name":"Item 5","id":5}]}]}]');
//$phpArray=json_decode('[{"url":"/","name":"home"},{"url":"/products-and-services","name":"products <br>and services","children":[{"url":"/products-and-services/design","name":"design","children":[{"url":"/products-and-services/design/graphics","name":"graphics"},{"url":"/products-and-services/design/web","name":"web"}]},{"url":"/products-and-services/seo","name":"seo"}]}]');


class getList{
	public $out='';
	public function makelist($objects)
	{

		foreach($objects as $object){
			$this->out.= '<li class="dd-item dd3-item" data-name="'.$object->name.'" data-url="'.$object->url.'"><div class="dd-handle dd3-handle">Drag</div><div class="dd3-content"><input class="name" value="'.$object->name.'"/><input class="url" value="'.$object->url.'"/><a class="remove">Remove</a></div>';

			if(isset($object->children)){
				 $this->out.= '<ol class="dd-list">';
				$this->makelist($object->children);
				$this->out.= '</ol>';
			}
			 $this->out.= '</li>';
		}
	}
}


if(isset($menu['data'])){
	$phpArray =unserialize($menu['data']);
}
$getList = new getList();
$getList->makelist($phpArray);	

?>
<style type="text/css">
.cf:after { visibility: hidden; display: block; font-size: 0; content: " "; clear: both; height: 0; }

/**
 * Nestable
 */
.dd { position: relative; display: block; margin: 0; padding: 0; max-width: 600px; list-style: none; font-size: 13px; line-height: 20px; }
.dd-list { display: block; position: relative; margin: 0; padding: 0; list-style: none; }
.dd-list .dd-list { padding-left: 30px; }
.dd-collapsed .dd-list { display: none; }
.dd-item,
.dd-empty,
.dd-placeholder { display: block; position: relative; margin: 0; padding: 0; min-height: 20px; font-size: 13px; line-height: 20px; }
.dd-handle { display: block; height: 30px; margin: 5px 0; padding: 5px 10px; color: #333; text-decoration: none; font-weight: bold; border: 1px solid #ccc;
    background: #fafafa;
    background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
    background:    -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
    background:         linear-gradient(top, #fafafa 0%, #eee 100%);
    -webkit-border-radius: 3px;
            border-radius: 3px;
    box-sizing: border-box; -moz-box-sizing: border-box;
}
.dd-handle:hover { color: #2ea8e5; background: #fff; }
.dd-item > button { display: block; position: relative; cursor: pointer; float: left; width: 25px; height: 20px; margin: 5px 0; padding: 0; text-indent: 100%; white-space: nowrap; overflow: hidden; border: 0; background: transparent; font-size: 12px; line-height: 1; text-align: center; font-weight: bold; }
.dd-item > button:before { content: '+'; display: block; position: absolute; width: 100%; text-align: center; text-indent: 0; }
.dd-item > button[data-action="collapse"]:before { content: '-'; }
.dd-placeholder,
.dd-empty { margin: 5px 0; padding: 0; min-height: 30px; background: #f2fbff; border: 1px dashed #b6bcbf; box-sizing: border-box; -moz-box-sizing: border-box; }
.dd-empty { border: 1px dashed #bbb; min-height: 100px; background-color: #e5e5e5;
    background-image: -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
                      -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
    background-image:    -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
                         -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
    background-image:         linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
                              linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
    background-size: 60px 60px;
    background-position: 0 0, 30px 30px;
}
.dd-dragel { position: absolute; pointer-events: none; z-index: 9999; }
.dd-dragel > .dd-item .dd-handle { margin-top: 0; }
.dd-dragel .dd-handle {
    -webkit-box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
            box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
}
/**
 * Nestable Extras
 */
.nestable-lists { display: block; clear: both; padding: 30px 0; width: 100%; border: 0; border-top: 2px solid #ddd; border-bottom: 2px solid #ddd; }
#nestable-menu { padding: 0; margin: 20px 0; }
#nestable-output,
#nestable2-output { width: 100%; height: 7em; font-size: 0.75em; line-height: 1.333333em; font-family: Consolas, monospace; padding: 5px; box-sizing: border-box; -moz-box-sizing: border-box; }
#nestable2 .dd-handle {
    color: #fff;
    border: 1px solid #999;
    background: #bbb;
    background: -webkit-linear-gradient(top, #bbb 0%, #999 100%);
    background:    -moz-linear-gradient(top, #bbb 0%, #999 100%);
    background:         linear-gradient(top, #bbb 0%, #999 100%);
}
#nestable2 .dd-handle:hover { background: #bbb; }
#nestable2 .dd-item > button:before { color: #fff; }
@media only screen and (min-width: 700px) {
    .dd { float: left; width: 48%; }
    .dd + .dd { margin-left: 2%; }
}
.dd-hover > .dd-handle { background: #2ea8e5 !important; }
/**
 * Nestable Draggable Handles
 */
.dd3-content { display: block; margin: 2px 0; padding: 3px 10px 1px 40px; color: #333; text-decoration: none; font-weight: bold; border: 1px solid #ccc;
    background: #fafafa;
    background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
    background:    -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
    background:         linear-gradient(top, #fafafa 0%, #eee 100%);
    -webkit-border-radius: 3px;
            border-radius: 3px;
    box-sizing: border-box; -moz-box-sizing: border-box;
}
.dd3-content:hover { color: #2ea8e5; background: #fff; }
.dd-dragel > .dd3-item > .dd3-content { margin: 0; }
.dd3-item > button { margin-left: 30px; }
.dd3-handle { position: absolute; margin: 0; left: 0; top: 0; cursor: pointer; width: 30px; text-indent: 210%; white-space: nowrap; overflow: hidden;
    border: 1px solid #aaa;
    background: #ddd;
    background: -webkit-linear-gradient(top, #ddd 0%, #bbb 100%);
    background:    -moz-linear-gradient(top, #ddd 0%, #bbb 100%);
    background:         linear-gradient(top, #ddd 0%, #bbb 100%);
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
	
	margin-top: 1px;
}
.dd3-handle:before { content: '='; display: block; position: absolute; left: 0; top: 3px; width: 100%; text-align: center; text-indent: 0; color: #fff; font-size: 20px; font-weight: normal; }
.dd3-handle:hover { background: #ddd; }
    </style>


<h1><?php echo$title;?></h1>
<p><strong>Add menu items</strong></p>
	
<button id="add" type="submit" class="">Add</button>
    <div class="cf nestable-lists">

        <div class="dd" id="nestable">
         
 <?php
echo '<ol class="dd-list">'.$getList->out.'</ol>';
?>
        </div>

    </div>

    <p><strong>Serialised Output (per list)</strong></p>
	<form action="" method="post" > 
	Menu name - assign this name when menu type is selected in article view <br>
	<input type="text" name="name" value="<?php echo $menu['name']?>">
	Dependent Apps - CSV for caching deletion 
	<input type="text" name="dependencies" value="<?php echo $menu['dependencies']?>">
    <textarea name="data" id="nestable-output"></textarea>
	<input type="submit" name="submit" value="<?php echo(isset($menu['name'])?'Update Menu':'Add Menu');?>">
	<?php if(isset($menu['name'])){?>
		<input type="submit" name="delete" value="Delete">
	<?php } ?>
	<input type="hidden" name="menuid" value="<?php echo $menu['menuid']?>">
	</form>
    <p>&nbsp;</p>
	
	Edit menus - <hr>
<?php
foreach($menus as $menu){
echo '<a href="'.$this->base_url.'siteadmin/menueditor?menuid='.$menu['id'].'">'.$menu['name'].'</a><br>';
}
?>
<hr>
  


<script src="/assets/js/jquery.nestable.js"></script>
<script>
$(document).ready(function()
{

$(document).on('keyup', 'input.name, input.url ', function(e){
	$(this).parent().parent().data($(this).attr('class'),$(this).val());
    updateOutput($('#nestable').data('output', $('#nestable-output')));
    updateOutput($('#nestable2').data('output', $('#nestable2-output')));
});

$('body').on('click','button#add',function(event){
 event.preventDefault(); 
$('#nestable .dd-list:first-child').prepend('<li class="dd-item dd3-item" data-name="new-item" data-url="/"><div class="dd-handle dd3-handle">Drag</div><div class="dd3-content"><input class="name" value="new name"/><input class="url" value="/url"/><a class="remove">Remove</a></div></li>');
    updateOutput($('#nestable').data('output', $('#nestable-output')));
});


$('body').on('click','a.remove',function(event){
 event.preventDefault(); 
        var element = $(this);
	
	element.parent().parent().remove();
    updateOutput($('#nestable').data('output', $('#nestable-output')));
	
}); 


    var updateOutput = function(e)
    {
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    };
    // activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
    })
    .on('change', updateOutput);
 
    // output initial serialised data
    updateOutput($('#nestable').data('output', $('#nestable-output')));
    updateOutput($('#nestable2').data('output', $('#nestable2-output')));

});


$(function(){ // this will be called when the DOM is ready
 
});
</script>


<div id="footer">
	  MVCMS - Copyright &copy; 2016 - Carl Turechek
  	
	 MVCMS is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    MVCMS is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with MVCMS.  If not, see <http://www.gnu.org/licenses/>.
		<hr />
	  <p class="small">Nestable List - Copyright &copy; <a href="http://dbushell.com/">David Bushell</a></p>
	  <p><strong><a href="https://github.com/dbushell/Nestable">Code on GitHub</a></strong></p>
	</div>





