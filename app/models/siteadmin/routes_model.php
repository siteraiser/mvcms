<?php /*
Copyright © 2016 
	
	This file is part of PHP-MVCMS.
    PHP-MVCMS is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    PHP-MVCMS is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with PHP-MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/	
class routes_model extends requestHandler{

	public function getRoutes(){
		return file_get_contents($this->doc_root.'app/system/config/routing.php');
	}
	public function putRoutes(){
		file_put_contents($this->doc_root.'app/system/config/routing.php',$_POST['routes'],LOCK_EX);
	}
}
