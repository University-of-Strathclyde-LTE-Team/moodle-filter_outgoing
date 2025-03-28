<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace filter_outgoing;

defined('MOODLE_INTERNAL') || die();

use core\files\curl_security_helper;
use core\output\notification;

class text_filter extends \core_filters\text_filter {

    const linkpattern = '/<\s*(?<tag>\w+)[^>]*\s+(?:src|href)\s*=\s*["\'](?<uri>[^"\']+)["\'][^>]*>/i';

    private $denylist = [
        '*.microsoftstream.com'
    ];

    protected $page;
    protected $context;
    protected $inlinetags = ['a', 'link'];

    public function setup($page, $context) {
        $this->page = $page;
        $this->context = $context;
    }
    protected function get_denied_hosts() {
        global $CFG;
        $denylist = get_config('filter_outgoing', 'denylist');
        $denylist = array_filter(array_map(
            'trim',
            explode("\n", $denylist)),
            function ($entry) {
                return !empty($entry);
            }
        );
        if(get_config('filter_outgoing', 'usecurlsecurityblockedhosts')) {
            if (isset($CFG->curlsecurityblockedhosts)) {
                $denylist = array_merge(
                    $denylist,
                    array_filter(
                        array_map(
                            'trim',
                            explode("\n", $CFG->curlsecurityblockedhosts)),
                        function ($entry) {
                            return !empty($entry);
                        }
                    )
                );
            }
        }
        return $denylist;
    }
    public function filter($text, array $options = array()) {
        global $OUTPUT;
        $usecache = get_config('filter_outgoing', 'usecache');

        $cache = \core_cache\cache::make('filter_outgoing', 'outgoingfiltercache');

        $contenthash = md5($text);
        if ($usecache && $cachedfilteredtext = $cache->get($contenthash)) {
            debugging("Existing filtered response found for content", DEBUG_DEVELOPER);
            return $cachedfilteredtext;
        }
        $r = $this->page->get_renderer('filter_outgoing');
        $url = $this->page->url->out();
        $canmanage = has_capability('moodle/course:manageactivities', $this->context);
        $strfiltered = get_string('filtered', 'filter_outgoing', (object)[
            'url' =>$url,
        ]);
        $strcontentfiltered = get_string('contentfiltered', 'filter_outgoing', (object)[
            'url' =>$url,
        ]);
        $filteredmessage = $OUTPUT->notification(
            $strfiltered,
            notification::NOTIFY_WARNING,
            false,
            $canmanage ? $strcontentfiltered: "",
            'req'
        );

        $isinline = false;
        $badtrap = new \moodle_url('/filter/outgoing/index.php');
        $badtrap = $badtrap->out();
        $denylist = $this->get_denied_hosts();
        $result = preg_replace_callback(self::linkpattern, function($matches) use ($denylist, $badtrap, $filteredmessage, $OUTPUT) {
            $inline = (in_array($matches['tag'], $this->inlinetags));
            $fulltext = $matches[0];
            foreach ($denylist as $deny) {
                $denyregex = str_replace(['*', '.'], ['.*', '\.'], $deny);
                $denyregex = '/' . $denyregex . '/i';
                if ($inline) {
                    $fulltext = str_replace($matches['uri'], $badtrap , $fulltext);
                } else {
                    if (preg_match($denyregex, $matches['uri'])) {
                        // Log outgoing link filtered.
                        $fulltext = str_replace($matches[0], $filteredmessage, $fulltext);
                    }
                }
            }
            if ($inline) {
                $fulltext .= $OUTPUT->pix_icon('req', 'core');
            }
            return $fulltext;
        }, $text);
        if ($isinline) {
            $result .=" (".$OUTPUT->pix_icon('req','core'). "content filtered)";
        }
        if ($usecache && $result !== $text) {
            debugging('adding to cache', DEBUG_DEVELOPER);
            $cache->set($contenthash, $result);
        }
        return $result;

    }
}
