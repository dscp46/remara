<?php $f3=Base::instance(); $curYear = date('Y'); ?>
    <main class="container mb-5">
      <div class="row">
	<h4 class="g-5 mb-4"><?= $APPNAME ?> &ndash; <?= $APPVER ?? '' ?></h4>
	<p>Copyright &copy; <?= ($curYear == $IRELYEAR) ? $IRELYEAR : "{$IRELYEAR} &ndash; {$curYear}" ?>, Célestine GRAMAIZE</p>
	<p>This program is free software: you can redistribute it and/or modify
    it under the terms of the <a href="https://www.gnu.org/licenses/gpl-3.0.html" rel="nofollow">GNU General Public License</a> as published by
    the Free Software Foundation, either version 3 of the License, or (at your option) any later version.</p>

        <p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.</p>

	<p>You should have received a copy of the GNU General Public License along with this program.  If not, see <a href="https://www.gnu.org/licenses/" rel="nofollow">https://www.gnu.org/licenses/</a>.</p>

	<h3 class="g-5"><?= $mui_about_addon_libs ?></h3>
	<div class"table-responsive">
	  <table class="table table-striped">
	    <tr>
	      <th scope="col"><?= $mui_about_software ?></th>
	      <th scope="col"><?= $mui_about_version ?></th>
	      <th scope="col"><?= $mui_about_license ?></th>
	      <th scope="col"><?= $mui_about_repo ?></th>
            </tr>
	    <tr>
              <td>Fat-free Framework</td>
              <td>3.8.2</td>
              <td>GNU GPL v3</td>
              <td><a href="https://github.com/bcosca/fatfree-core/" rel="nofollow">F3 on Github</a></td>
	    </tr>          
	    <tr>
              <td>F3 Access plugin</td>
              <td>0.2.1</td>
              <td>GNU GPL v3</td>
              <td><a href="https://github.com/xfra35/f3-access" rel="nofollow">F3 Access on Github</a></td>
	    </tr>          
	    <tr>
              <td>Bootstrap</td>
              <td>5.2</td>
              <td>MIT</td>
              <td><a href="https://github.com/twbs/bootstrap" rel="nofollow">Bootstrap on Github</a></td>
	    </tr>          
          </table>
	</div>
      </div>
    </main>
