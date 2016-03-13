<?php 
/*	PHP-MVCMS - Copyright © 2016 

    PHP-MVCMS is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PHP-MVCMS is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
  // $startTime = microtime(true);  

error_reporting(0);
function class_loader($class){
require('app/classes/' . $class . '.php');//---- might be a badly named model if gives error here   
}
spl_autoload_register('class_loader');

$rh = new requestHandler();

$rh->getContent();
echo$rh->output;


// $endTime = microtime(true);  
//   $elapsed = $endTime - $startTime;
//   echo "Execution time : $elapsed seconds";
