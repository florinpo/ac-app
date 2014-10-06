<div class="widget grid4">
    <div class="whead"><h6>Small buttons</h6><div class="clear"></div></div>
    <div class="body" align="center">
        <a href="#" class="buttonS bDefault mb10 mt5">Button</a>
        <a href="#" class="buttonS bGreen ">Button</a>
        <a href="#" class="buttonS bBlack  mb10">Button</a>
        <?php echo CHtml::submitButton(t('site', 'Rispondi'), array('class' => 'buttonM bDefault')); ?>
    </div>
</div>

<div class="widget">
    <div class="whead"><h6>Buttons with icons (images)</h6><div class="clear"></div></div>
    <div class="body" align="center">
        <a href="#" class="buttonM bDefault"><span class="icond-circle-x"></span><span>Button</span></a>
        <a href="#" class="buttonM bGreen"><span class="iconl-stats"></span><span>Button</span></a>
        <a href="#" class="buttonM bBlack"><span class="iconl-user"></span><span>Button</span></a>
    </div>
</div>

<!-- Buttons with dd -->
<div class="widget grid4">
    <div class="whead"><h6>Buttons with dropdown</h6><div class="clear"></div></div>
    <div class="body" align="center">
        <div class="btn-group" style="display: inline-block; margin-bottom: -4px;">
            <a class="buttonS bDefault" data-toggle="dropdown" href="#">Actions<span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="#"><span class="icos-add"></span>Add</a></li>
                <li><a href="#"><span class="icos-trash"></span>Remove</a></li>
                <li><a href="#" class=""><span class="icos-pencil"></span>Edit</a></li>
                <li><a href="#" class=""><span class="icos-heart"></span>Do whatever you like</a></li>
            </ul>
        </div>

        <div class="btn-group rightdd" style="display: inline-block; margin-bottom: -4px;">
            <button class="buttonS bDefault floatL">Button</button>
            <button class="buttonS bDefault dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><a href="#"><span class="icos-add"></span>Add</a></li>
                <li><a href="#"><span class="icos-trash"></span>Remove</a></li>
                <li><a href="#" class=""><span class="icos-pencil"></span>Edit</a></li>
                <li><a href="#" class=""><span class="icos-heart"></span>Do whatever you like</a></li>
            </ul>
        </div> 

        <div class="btn-group dropup" style="display: inline-block; margin-bottom: -4px;">
            <a class="buttonS bDefault" data-toggle="dropdown" href="#">Actions<span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="#"><span class="icos-add"></span>Add</a></li>
                <li><a href="#"><span class="icos-trash"></span>Remove</a></li>
                <li><a href="#" class=""><span class="icos-pencil"></span>Edit</a></li>
                <li><a href="#" class=""><span class="icos-heart"></span>Do whatever you like</a></li>
            </ul>
        </div>

        <div class="btn-group dropup rightdd" style="display: inline-block; margin-bottom: -4px;">
            <button class="buttonS bDefault floatL">Button</button>
            <button class="buttonS bDefault dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><a href="#"><span class="icos-add"></span>Add</a></li>
                <li><a href="#"><span class="icos-trash"></span>Remove</a></li>
                <li><a href="#" class=""><span class="icos-pencil"></span>Edit</a></li>
                <li><a href="#" class=""><span class="icos-heart"></span>Do whatever you like</a></li>
            </ul>
        </div> 
        <div class="clear"></div>
    </div>
</div>

<div class="widget">
    <div class="whead"><h6>Other buttons (images)</h6><div class="clear"></div></div>
    <div class="body" align="center">

        <!-- Toolbar with image -->
        <ul class="btn-group toolbar"  style="float: left; margin-left: 15px;">
            <li><a href="#" class="buttonM bDefault">btn</a></li>
            <li><a href="#" class="buttonM bDefault">btn</a></li>
            <li><a href="#" class="buttonM bDefault">btn</a></li>
        </ul> 

        <div class="clear"></div>
    </div>
</div>