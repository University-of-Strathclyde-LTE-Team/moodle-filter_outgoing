<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace filter_outgoing\renderable;
use core\output\pix_icon;
use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use core\output\help_icon ;
class warn_icon extends help_icon implements renderable, templatable {
    public function export_for_template(renderer_base $output) {
        $help_icon = parent::export_for_template($output);
        $help_icon->icon = (new pix_icon('req', $help_icon->alt, 'core'))->export_for_template($output);
        return $help_icon;
    }
}
