
<ul>
    <li class="xhsCategoryHeading"><a href="%SHOPURL%">%SHOPHEADING%</a>
        <?php if(count($this->shopPages) > 0) { ?>
        <ul>
            <?php
            $previousLevel = (int)$this->shopLevel;
            $i = 0;
            foreach($this->shopPages as $page){
                $nextLevel = isset($this->shopPages[$i + 1]) ? (int)$this->shopPages[$i + 1]['level'] :  (int)$this->shopLevel; ?>
                <?php echo ($previousLevel > $page['level']) ? '</ul></li>' : '</li>' ; ?>
            <!--<li><a href="<?php echo $page['url']; ?>"><?php echo $page['heading']; ?></a> -->
            <li><a href="%SHOPURL%&xhsCategory=<?php echo urlencode($page['heading']); ?>"><?php echo $page['heading']; ?></a>
            <?php echo ($nextLevel > $page['level']) ? "\n\t" . '<ul>' : '</li>' ; ?>
            <?php if(!isset($this->shopPages[$i + 1])){echo '</li>' ; break;} ; ?>


            <?php

            $i++;

            $previousLevel = $page['level'];
        } ?>

        </ul>
        <?php } ?>
    </li>
</ul>

