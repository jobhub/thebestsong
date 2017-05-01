<?php /* Smarty version 2.6.9, created on 2014-05-14 10:19:03
         compiled from home_page.html */ ?>
<div id="search-page">
    <table width="100%" style="table-layout:fixed;padding-top: 230px;">
        <tr>
            <td style="width: 100px;overflow: hidden;">
                <img style="border: 0px; margin-bottom: -2px;" src="<?php echo $this->_tpl_vars['CONFIG_CSS']; ?>
/img/wave.gif">
            </td>					
            <td>
                <h1>WHAT IS THE BEST SONG?</h1>
            </td>
            <td style="width: 100px;overflow: hidden;">
                <img style="border: 0px; margin-bottom: -2px;" src="<?php echo $this->_tpl_vars['CONFIG_CSS']; ?>
/img/wave.gif">
            </td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td>
                <div class="src_d">
                    <div class="src_di">
                        <input type="button" value="" name="go" class="src_l">
                        <input type="text" value=""  name="q" autocomplete="off" placeholder="Enter an album, artist, #tag...">         
                        <select id="search_category" class="search_category">
                            <option value="0">EXPERTS</option>
                            <option value="1">MENTORS</option>
                            <option value="2">MAN</option>
                            <option value="3">WOMEN</option>
                            <option value="4">FRENCH</option>
                            <option value="5">ENGLISH</option>
                            <option value="6">AMERICANS</option>
                            <option value="7">VEGGIES</option>
                            <option value="8">Filter by&nbsp;&nbsp;  ALL CATEGORIES</option>
                        </select>
                    </div>
                </div>
            </td>					
            <td>
                <input class="yellow" style="width: 100%;" type="button" value="TELL ME">
            </td>           
        </tr>
    </table>
    <div class="access">
        <ul id="quicklink">
            <li><a class="border" href="#recent">RECENT</a></li>
            <li><a class="border" href="#battle">BATTLES</a></li>
            <li><a href="#highlight">HIGHLIGHTS</a></li>
        </ul>
    </div>        
</div>