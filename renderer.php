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

//namespace filter_outgoing;

use filter_outgoing\renderable\warn_icon;

class filter_outgoing_renderer extends \plugin_renderer_base {
    public function warn_icon($identifier, $component = 'moodle', $linktext = '', $a = null) {
        $icon = new warn_icon($identifier, $component, $a);
        $icon->diag_strings();
        if ($linktext === true) {
            $icon->linktext = get_string($icon->identifier, $icon->component, $a);
        } else if (!empty($linktext)) {
            $icon->linktext = $linktext;
        }
        return $this->render($icon);
    }
}
