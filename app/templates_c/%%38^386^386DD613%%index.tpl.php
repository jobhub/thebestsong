<?php /* Smarty version 2.6.9, created on 2014-05-14 10:19:03
         compiled from C:%5Cxampp%5Chtdocs%5Cthebestsong%5Capp%5Cconfig/../../app/views/index/index.tpl */ ?>
<?php echo '<?xml'; ?>
 version="1.0" encoding="utf-8"<?php echo '?>'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $this->_tpl_vars['APPLICATION_TITLE']; ?>
 <?php echo $this->_tpl_vars['APPLICATION_VERSION']; ?>
</title>
        <link href="<?php echo $this->_tpl_vars['CONFIG_CSS']; ?>
/images/favicon.ico" type="image/x-icon" rel="shortcut icon"/>
        <link  rel ="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['CONFIG_CSS']; ?>
/style.css?v1"/>
        <link  rel ="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['CONFIG_CSS']; ?>
/component.css"/>
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['CONFIG_JS']; ?>
/jquery/jquery-1.10.1.min.js"></script>              
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['CONFIG_JS']; ?>
/jquery/jquery.ddslick.min.js"></script>        
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['CONFIG_JS']; ?>
/jquery/jquery-ui-1.9.2.custom.min.js"></script>        
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['CONFIG_JS']; ?>
/jquery/jquery.hashchange.js"></script>
        <script src="<?php echo $this->_tpl_vars['CONFIG_JS']; ?>
/modernizr.custom.js"></script>
    </head>
    
    <body id="home" class="cbp-spmenu-push" style="overflow-y: hiddena;">
        <div id="main" class="relative">
            <div id="header"><div class="logo"><img id="logo" src="<?php echo $this->_tpl_vars['CONFIG_CSS']; ?>
/img/scroll_logo.png"></div></div>
            <div class="wrap fixed"></div>
            <div class="content absolute">
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "home_page.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "recent_page.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>                                
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "battle_page.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>   
                
                <div style="height: 56px;width:100%;display: block;"></div>
            </div>
            <div id="footer"></div>
        </div>
        
        <div class="nextPage">        
            <a href="#recent"><img src="<?php echo $this->_tpl_vars['CONFIG_CSS']; ?>
/img/arrow.png"></a>
        </div>
                
        <a id="showLeftPush" class="next fixed"></a>
        <nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="cbp-spmenu-s1">                
            <a href="#">HOME</a>
            <a href="#">RECENT</a>
            <a href="#">BATTLES</a>
            <a href="#">HIGHLIGHTS</a>
            <a href="#">PEOPLE TO FOLLOW</a>
            <a href="#">MY PROFIL</a>
            <a href="#">BEST LISTS</a>
        </nav>
        <script type="text/javascript">
            <?php echo '
                $(\'.search_category\').ddslick();
                var current_page = \'home\';                
                var pages = new Array();
                    pages[0] = "home";
                    pages[1] = "recent";
                    pages[2] = "battle";
                    pages[3] = "highlight";

                $( document ).ready(function() {                                        
                    $(\'#search_category\').ddslick(\'select\', {index: 8 });
                    $(\'.dd-option-value[value="8"]\').next().text(\'ALL CATEGORIES\');
                    
                    //$(\'#content\').height($(\'body\').height() + 112);
                    $(\'#search-page\').height($(\'.wrap\').height());
                    $(\'#recent-page\').height($(\'.wrap\').height());                    
                    $(\'#battle-page\').height($(\'.wrap\').height()); 
                }); 
                    
                function setNextPrev(page){                                            
                    var now = null;
                    var after = null;
                    var i=0;
                    
                    while (now == null || after == null){
                        if (pages[i] == current_page){ 
                            now = i;                       
                        }
                            
                        if (pages[i] == page){
                            after = i;                             
                            $(\'.nextPage\').find(\'a\').attr("href", "#"+pages[i+1]);                          
                        }
                        
                        i++;
                    }
                    
                    current_page = page;
                    //alert(now+\' \'+after);
                    if (now < after) {
                        var dif = after - now;
                        var multi = dif * $(\'.content\').height();
                        return -multi;
                    }
                    if (now > after) {
                        var dif = now - after;
                        var multi = $(\'.content\').height() / dif;
                        return multi;
                    }
                }
                    
                function showPage(page) {                   
                    var top = setNextPrev(page);                 
                          
                    var position = $(\'#\' + page + \'-page\').position();                        
                        
                    $(\'body,html\').animate({
                        scrollTop: position.top
                    }, { duration: 650, queue: false , complete: function(){
                        //$(\'.content\').offset({ top: top})
                        //$(\'#search-page\').css("margin-top", top);                                           
                    }}); 
                                        
                    $(\'#logo\').animate({"left":"56px"}, { duration: 650, queue: false });

                    //setTimeout(\'showPageElements(\\\'\'+page+\'\\\')\', 900);   
                }
                
                $(function() {               
                    $(window).hashchange(function() {
                        hash = location.hash;

                        page = hash.replace(/^#/, \'\');

                        // Iterate over all nav links, setting the "selected" class as-appropriate.
                        $(\'.main-nav a\').each(function(){
                            var that = $(this);
                            that[ that.attr( \'href\' ) === hash ? \'addClass\' : \'removeClass\' ]( \'selected\' );
                        });

                        if (page != \'\')
                            showPage(page);                      
                    })
                })
            '; ?>

        </script>
        <script src="<?php echo $this->_tpl_vars['CONFIG_JS']; ?>
/classie.js"></script>
        <script style="text/javascript">
            <?php echo '
                
                window.onscroll = function (e) {  
                    if ($(\'#showLeftPush\').hasClass(\'active\')){
                        $(\'#showLeftPush\').css("margin-top", $(document).scrollTop()); 
                    }
                } 
                
                var menuLeft = document.getElementById( \'cbp-spmenu-s1\' ),				
                        showLeftPush = document.getElementById( \'showLeftPush\' ),				
                        body = document.body;


                showLeftPush.onclick = function() {
                        if ($(\'#showLeftPush\').hasClass(\'active\')){}
                        else {
                            
                            $(\'#showLeftPush\').css("margin-top", $(document).scrollTop());    
                       
                            $(\'.nextPage\').addClass(\'absolute\');
                            $(\'.nextPage\').removeClass(\'fixed\');                  
                                
                            $(\'.next\').addClass(\'absolute\');
                            $(\'.next\').removeClass(\'fixed\');
                        }
                            
                        classie.toggle( this, \'active\' );
                        classie.toggle( body, \'cbp-spmenu-push-toright\' );
                        classie.toggle( menuLeft, \'cbp-spmenu-open\' );
                        //disableOther( \'showLeftPush\' );
                            
                        if ($(\'#showLeftPush\').hasClass(\'active\')){}
                        else {
                            var timer = setTimeout(function(){                                                                
                          
                                $(\'.nextPage\').addClass(\'fixed\');
                                $(\'.nextPage\').removeClass(\'absolute\');
                    
                                    
                                $(\'.next\').addClass(\'fixed\');
                                $(\'.next\').removeClass(\'absolute\');
                                    
                                $(\'#showLeftPush\').css("margin-top", 0);
                            },400);                            
                        }
                };
            '; ?>

        </script>
    </body>
</html>