<span class="com-sergiosgc-tabset com-sergiosgc-tabset-<?php echo $_REQUEST['name'] ?>">
 <span class="com-sergiosgc-tabset-tabs"><?php 
foreach ($_REQUEST['tabset']['tabs'] as $tab) 
printf('<a href="#" class="com-sergiosgc-tabset-tab %s" data-tabname="%s">%s</a>', 
$_REQUEST['active'] == $tab['name'] ? "com-sergiosgc-tab-active" : "com-sergiosgc-tab-inactive",
$tab['name'], 
$tab['label']);
?></span>
 <span class="com-sergiosgc-tabset-tabcontent"><?php echo $_REQUEST['content'] ?></span>
 <script type="text/javascript">
window.addEventListener('DOMContentLoaded', (function(currentScript) {
  var tabSet = currentScript.parentNode;
  var tabSetTabs = (function(snapshot) { 
    let result=[];
    for(var i=0; i<snapshot.snapshotLength; i++) result.push(snapshot.snapshotItem(i)); 
    return result;
  })(document.evaluate("./span", tabSet, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE)).filter( span => span.classList.contains("com-sergiosgc-tabset-tabs"))[0];
  var tabSetTabContent = (function(snapshot) { 
    let result=[];
    for(var i=0; i<snapshot.snapshotLength; i++) result.push(snapshot.snapshotItem(i)); 
    return result;
  })(document.evaluate("./span", tabSet, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE)).filter( span => span.classList.contains("com-sergiosgc-tabset-tabcontent"))[0];
  var tabSetTabButtons = (function(snapshot) { 
    let result=[];
    for(var i=0; i<snapshot.snapshotLength; i++) result.push(snapshot.snapshotItem(i)); 
    return result;
  })(document.evaluate("./a", tabSetTabs, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE)).filter( a => a.classList.contains("com-sergiosgc-tabset-tab"));
  var tabSetTabContentTabs = (function(snapshot) { 
    let result=[];
    for(var i=0; i<snapshot.snapshotLength; i++) result.push(snapshot.snapshotItem(i)); 
    return result;
  })(document.evaluate("./div", tabSetTabContent, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE)).filter( div => div.classList.contains("com-sergiosgc-tab"));
  tabSetTabButtons.map( function(btn) {
    btn.addEventListener('click', function(ev) {
      ev.preventDefault();
      if (ev.target.classList.contains("com-sergiosgc-tab-active")) return;
      tabSetTabButtons.map(function(btn) {
        btn.classList.remove( ev.target.dataset.tabname == btn.dataset.tabname ? "com-sergiosgc-tab-inactive" : "com-sergiosgc-tab-active")
        btn.classList.add( ev.target.dataset.tabname == btn.dataset.tabname ? "com-sergiosgc-tab-active" : "com-sergiosgc-tab-inactive")
      })
      tabSetTabContentTabs.map(function(tab) {
        tab.classList.remove( ev.target.dataset.tabname == tab.dataset.name ? "com-sergiosgc-tab-inactive" : "com-sergiosgc-tab-active")
        tab.classList.add( ev.target.dataset.tabname == tab.dataset.name ? "com-sergiosgc-tab-active" : "com-sergiosgc-tab-inactive")
      })
    });
  });
}).bind(this, document.currentScript));
 </script>
</span>