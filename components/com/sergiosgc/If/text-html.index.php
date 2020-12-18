<?php
if ( ( (bool) $_REQUEST['test'] ) xor (isset($_REQUEST['not']) xor isset($_REQUEST['else']))) print($_REQUEST['content']);
