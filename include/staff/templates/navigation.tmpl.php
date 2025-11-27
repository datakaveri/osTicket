<?php
if($nav && ($tabs=$nav->getTabs()) && is_array($tabs)){
    foreach($tabs as $name =>$tab) {
        if ($tab['href'][0] != '/')
            $tab['href'] = ROOT_PATH . 'scp/' . $tab['href'];
        echo sprintf('<li class="%s %s"><a href="%s">%s</a>',
            isset($tab['active']) ? 'active':'inactive',
            @$tab['class'] ?: '',
            $tab['href'],$tab['desc']);
        if(($subnav=$nav->getSubMenu($name))){
            echo "<ul>\n";
            foreach($subnav as $k => $item) {
                // Skip if item is not an array
                if (!is_array($item)) {
                    continue;
                }
                
                if (isset($item['id']) && !($id=$item['id']))
                    $id="nav$k";
                
                // Check if this submenu item is active BEFORE modifying href
                $itemClass = $item['iconclass'] ?? '';
                $currentScript = basename($_SERVER['SCRIPT_NAME']);
                
                // Safely parse the href
                $itemHrefBase = '';
                if (isset($item['href']) && is_string($item['href'])) {
                    $parsedUrl = parse_url($item['href'], PHP_URL_PATH);
                    $itemHrefBase = $parsedUrl ? basename($parsedUrl) : '';
                }
                
                $isActive = false;
                if ($itemHrefBase && $currentScript == $itemHrefBase) {
                    $isActive = true;
                } elseif (isset($item['urls']) && is_array($item['urls'])) {
                    foreach ($item['urls'] as $url) {
                        if ($currentScript == basename($url)) {
                            $isActive = true;
                            break;
                        }
                    }
                }
                
                if ($isActive) {
                    $itemClass .= ' active';
                }
                
                // Now modify href for output
                if (isset($item['href']) && is_string($item['href']) && $item['href'][0] != '/')
                    $item['href'] = ROOT_PATH . 'scp/' . $item['href'];

                echo sprintf(
                    '<li><a class="%s" href="%s" title="%s" id="%s">%s</a></li>',
                    $itemClass,
                    $item['href'] ?? '#', 
                    $item['title'] ?? null,
                    $id ?? null, 
                    $item['desc'] ?? '');
            }
            echo "\n</ul>\n";
        }
        echo "\n</li>\n";
    }
} ?>
