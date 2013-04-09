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

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_vanguard
 * @copyright  2012
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class theme_vanguard_core_renderer extends core_renderer {

    /*
     * This renders a notification message.
     * Uses bootstrap compatible html.
     */
    public function notification($message, $classes = 'notifyproblem') {
        $message = clean_text($message);
        $type = '';

        if ($classes == 'notifyproblem') {
            $type = 'alert alert-error';
        }
        if ($classes == 'notifysuccess') {
            $type = 'alert alert-success';
        }
        if ($classes == 'notifymessage') {
            $type = 'alert alert-info';
        }
        if ($classes == 'redirectmessage') {
            $type = 'alert alert-block alert-info';
        }
        return "<div class=\"$type\">$message</div>";
    }

        /*
         * This renders the navbar.
         * Uses bootstrap compatible html.
         */
        public function navbar() {
            $items = $this->page->navbar->get_items();
            foreach ($items as $item) {
                $item->hideicon = true;
                    $breadcrumbs[] = $this->render($item);
            }
            $divider = '<span class="divider">/</span>';
            $list_items = '<li>'.join(" $divider</li><li>", $breadcrumbs).'</li>';
            $title = '<span class="accesshide">'.get_string('pagepath').'</span>';
            return $title . "<ul class=\"breadcrumb\">$list_items</ul>";
    }

        /**
         * The standard tags (typically performance information and validation links,
         * if we are in developer debug mode) that should be output in the footer area
         * of the page. Designed to be called in theme layout.php files.
         *
         * @return string HTML fragment.
         */
        public function standard_footer_html() {
            global $CFG, $SCRIPT;

            // This function is normally called from a layout.php file in {@link core_renderer::header()}
            // but some of the content won't be known until later, so we return a placeholder
            // for now. This will be replaced with the real content in {@link core_renderer::footer()}.
            $output = $this->unique_performance_info_token;
            if ($this->page->devicetypeinuse == 'legacy') {
                // The legacy theme is in use print the notification
                $output .= html_writer::tag('div', get_string('legacythemeinuse'), array('class'=>'legacythemeinuse'));
            }

            // Get links to switch device types (only shown for users not on a default device)
            $output .= $this->theme_switch_links();

            if (!empty($CFG->debugpageinfo)) {
                $output .= '<div class="performanceinfo pageinfo well"><i class="icon-cog"></i>&nbsp;&nbsp;This page is: ' . $this->page->debug_summary() . '</div>';
            }
            if (debugging(null, DEBUG_DEVELOPER) and has_capability('moodle/site:config', context_system::instance())) {  // Only in developer mode
                // Add link to profiling report if necessary
                if (function_exists('profiling_is_running') && profiling_is_running()) {
                    $txt = get_string('profiledscript', 'admin');
                    $title = get_string('profiledscriptview', 'admin');
                    $url = $CFG->wwwroot . '/admin/tool/profiling/index.php?script=' . urlencode($SCRIPT);
                    $link= '<a title="' . $title . '" href="' . $url . '">' . $txt . '</a>';
                    $output .= '<div class="profilingfooter">' . $link . '</div>';
                }
                $output .= '<div class="purgecaches"><a class="btn btn-small" href="'.$CFG->wwwroot.'/'.$CFG->admin.'/purgecaches.php?confirm=1&amp;sesskey='.sesskey().'"><i class="icon-trash"></i>&nbsp;&nbsp;'.get_string('purgecaches', 'admin').'</a></div>';
            }
            if (!empty($CFG->debugvalidators)) {
                // NOTE: this is not a nice hack, $PAGE->url is not always accurate and $FULLME neither, it is not a bug if it fails. --skodak
                $output .= '<div class="validators"><ul>
                  <li><a class="btn btn-small btn-info" href="http://validator.w3.org/check?verbose=1&amp;ss=1&amp;uri=' . urlencode(qualified_me()) . '"><i class="icon-cog icon-white"></i>&nbsp;&nbsp;Validate HTML</a></li>
                  <li><a class="btn btn-small btn-info" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=-1&amp;url1=' . urlencode(qualified_me()) . '"><i class="icon-cog icon-white"></i>&nbsp;&nbsp;Section 508 Check</a></li>
                  <li><a class="btn btn-small btn-info" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=0&amp;warnp2n3e=1&amp;url1=' . urlencode(qualified_me()) . '"><i class="icon-cog icon-white"></i>&nbsp;&nbsp;WCAG 1 (2,3) Check</a></li>
                </ul><br /></div>';
            }
            if (!empty($CFG->additionalhtmlfooter)) {
                $output .= "\n".$CFG->additionalhtmlfooter;
            }
            return $output;
        }

        /**
         * Return the 'back' link that normally appears in the footer.
         *
         * @return string HTML fragment.
         */
        public function home_link() {
            global $CFG, $SITE;

            if ($this->page->pagetype == 'site-index') {
                // Special case for site home page - please do not remove
                return '<div class="sitelink">' .
                       '<a title="Moodle" href="http://moodle.org/">' .
                       '<img style="width:100px;height:30px" src="' . $this->pix_url('moodlelogo') . '" alt="moodlelogo" /></a></div>';

            } else if (!empty($CFG->target_release) && $CFG->target_release != $CFG->release) {
                // Special case for during install/upgrade.
                return '<div class="sitelink">'.
                       '<a title="Moodle" href="http://docs.moodle.org/en/Administrator_documentation" onclick="this.target=\'_blank\'">' .
                       '<img style="width:100px;height:30px" src="' . $this->pix_url('moodlelogo') . '" alt="moodlelogo" /></a></div>';

            } else if ($this->page->course->id == $SITE->id || strpos($this->page->pagetype, 'course-view') === 0) {
                return '<div class="homelink"><a class="btn btn-small" href="' . $CFG->wwwroot . '/"><i class="icon-home"></i>&nbsp;&nbsp;' .
                        get_string('home') . '</a></div>';

            } else {
                return '<div class="homelink"><a class="btn btn-small" href="' . $CFG->wwwroot . '/course/view.php?id=' . $this->page->course->id . '"><i class="icon-home"></i>&nbsp;&nbsp;' .
                        format_string($this->page->course->shortname, true, array('context' => $this->page->context)) . '</a></div>';
            }
        }

         /**
         * Redirects the user by any means possible given the current state
         *
         * This function should not be called directly, it should always be called using
         * the redirect function in lib/weblib.php
         *
         * The redirect function should really only be called before page output has started
         * however it will allow itself to be called during the state STATE_IN_BODY
         *
         * @param string $encodedurl The URL to send to encoded if required
         * @param string $message The message to display to the user if any
         * @param int $delay The delay before redirecting a user, if $message has been
         *         set this is a requirement and defaults to 3, set to 0 no delay
         * @param boolean $debugdisableredirect this redirect has been disabled for
         *         debugging purposes. Display a message that explains, and don't
         *         trigger the redirect.
         * @return string The HTML to display to the user before dying, may contain
         *         meta refresh, javascript refresh, and may have set header redirects
         */
        public function redirect_message($encodedurl, $message, $delay, $debugdisableredirect) {
            global $CFG;
            $url = str_replace('&amp;', '&', $encodedurl);

            switch ($this->page->state) {
                case moodle_page::STATE_BEFORE_HEADER :
                    // No output yet it is safe to delivery the full arsenal of redirect methods
                    if (!$debugdisableredirect) {
                        // Don't use exactly the same time here, it can cause problems when both redirects fire at the same time.
                        $this->metarefreshtag = '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />'."\n";
                        $this->page->requires->js_function_call('document.location.replace', array($url), false, ($delay + 3));
                    }
                    $output = $this->header();
                    break;
                case moodle_page::STATE_PRINTING_HEADER :
                    // We should hopefully never get here
                    throw new coding_exception('You cannot redirect while printing the page header');
                    break;
                case moodle_page::STATE_IN_BODY :
                    // We really shouldn't be here but we can deal with this
                    debugging("You should really redirect before you start page output");
                    if (!$debugdisableredirect) {
                        $this->page->requires->js_function_call('document.location.replace', array($url), false, $delay);
                    }
                    $output = $this->opencontainers->pop_all_but_last();
                    break;
                case moodle_page::STATE_DONE :
                    // Too late to be calling redirect now
                    throw new coding_exception('You cannot redirect after the entire page has been generated');
                    break;
            }
            $output .= $this->notification($message, 'redirectmessage');
            $output .= '<div class="continuebutton"><a class="btn btn-small btn-info" href="'. $encodedurl .'">'. get_string('continue') .'&nbsp;&nbsp;<i class="icon-forward icon-white"></i></a></div>';
            if ($debugdisableredirect) {
                $output .= '<p><strong>Error output, so disabling automatic redirect.</strong></p>';
            }
            $output .= $this->footer();
            return $output;
    }

        /**
         * Returns HTML to display a "Turn editing on/off" button in a form.
         *
         * @param moodle_url $url The URL + params to send through when clicking the button
         * @return string HTML the button
         */
        public function edit_button(moodle_url $url) {

            $url->param('sesskey', sesskey());
            if ($this->page->user_is_editing()) {
                $url->param('edit', 'off');
                $text = '<a href="'.$url.'" class="btn btn-danger" title="'.get_string('turneditingoff').'"><i class="icon-off icon-white"></i></a>';
            } else {
                $url->param('edit', 'on');
                $text = '<a href="'.$url.'" class="btn btn-success"  title="'.get_string('turneditingon').'"><i class="icon-edit icon-white"></i></a>';
            }

            return ($text);
    }


    /*
     * Overriding the custom_menu function ensures the custom menu is
     * always shown, even if no menu items are configured in the global
     * theme settings page.
     * We use the sitename as the first menu item.
     */
    public function custom_menu($custommenuitems = '') {
        global $CFG;

        if (!empty($CFG->custommenuitems)) {
            $custommenuitems .= $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    /*
     * This renders the bootstrap top menu.
     *
     * This renderer is needed to enable the Bootstrap style navigation.
     */
    protected function render_custom_menu(custom_menu $menu) {
        // If the menu has no children return an empty string.
        if (!$menu->has_children()) {
            return '';
        }
                // Add a login or logout link
                if (isloggedin()) {
                    $branchlabel = get_string('logout');
                    $branchurl   = new moodle_url('/login/logout.php');
                } else {
                    $branchlabel = get_string('login');
                    $branchurl   = new moodle_url('/login/index.php');
                }
                $branch = $menu->add($branchlabel, $branchurl, $branchlabel, -1);



        $addlangmenu = true;
        $langs = get_string_manager()->get_list_of_translations();
            if ($this->page->course != SITEID and !empty($this->page->course->lang)) {
            // Do not show lang menu if language forced.
            $addlangmenu = false;
        }
        if (count($langs) < 2) {
            $addlangmenu = false;
        }

        if ($addlangmenu) {
            $language = $menu->add(get_string('language'), new moodle_url('#'), get_string('language'), 10000);
            foreach ($langs as $langtype => $langname) {
                $language->add($langname,
                new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        $content = '<ul class="nav">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        return $content.'</ul>';
    }

    /*
     * This code renders the custom menu items for the
     * bootstrap dropdown menu.
     */
    protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0 ) {
        static $submenucount = 0;

        if ($menunode->has_children()) {

            if ($level == 1) {
                $dropdowntype = 'dropdown';
            } else {
                $dropdowntype = 'dropdown-submenu';
            }

            $content = html_writer::start_tag('li', array('class'=>$dropdowntype));
            // If the child has menus render it as a sub menu.
            $submenucount++;
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_'.$submenucount;
            }
            $content .= html_writer::start_tag('a', array('href'=>$url, 'class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'));
            $content .= $menunode->get_title().'&nbsp;';
            if ($level == 1) {
                $content .= '<b class="caret"></b>';
            }
            $content .= '</a>';
            $content .= '<ul class="dropdown-menu">';
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 0);
            }
            $content .= '</ul>';
        } else {
            $content = '<li>';
            // The node doesn't have children so produce a final menuitem.
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#';
            }
            $content .= html_writer::link($url, $menunode->get_text(), array('title'=>$menunode->get_title()));
        }
        return $content;
    }

       /*
        * This code replaces the standard moodle icons
        * with a icon sprite that is included in bootstrap
        * If the icon is not listed in the $icons array
        * the original Moodle icon will be shown
        */

        static $icons = array(
            'docs' => 'question-sign',
            'book' => 'book',
            'chapter' => 'file',
            'spacer' => 'spacer',
            'generate' => 'gift',
            'add' => 'plus',
            't/assignroles' => 'user',
            't/hide' => 'eye-open',
            'i/hide' => 'eye-open',
            't/show' => 'eye-close',
            'i/show' => 'eye-close',
            't/add' => 'plus',
            't/right' => 'arrow-right',
            't/left' => 'arrow-left',
            't/up' => 'arrow-up',
            't/down' => 'arrow-down',
            't/edit' => 'edit',
            't/editstring' => 'pencil',
            't/copy' => 'retweet',
            't/delete' => 'remove',
            'i/edit' => 'edit',
            'i/settings' => 'list-alt',
            'i/grades' => 'grades',
            'i/group' => 'user',
            't/groupn' => 'remove-circle',
            't/groups' => 'ok-circle',
            't/groupv' => 'ok-circle',
            't/switch_plus' => 'plus-sign',
            't/switch_minus' => 'minus-sign',
            'i/filter' => 'filter',
            't/move' => 'move',
            'i/move_2d' => 'move',
            'i/backup' => 'cog',
            'i/restore' => 'cog',
            'i/return' => 'repeat',
            'i/reload' => 'refresh',
            'i/roles' => 'user',
            'i/user' => 'user',
            'i/users' => 'user',
            'i/publish' => 'publish',
            'i/navigationitem' => 'chevron-right' );


        public function block_controls($controls) {
            if (empty($controls)) {
                return '';
            }
            $controlshtml = array();
            foreach ($controls as $control) {
                $controlshtml[] = self::a(array('href'=>$control['url'], 'title'=>$control['caption']), self::moodle_icon($control['icon']));
            }
            return self::div(array('class'=>'commands'), implode($controlshtml));
        }

        protected static function a($attributes, $content) {
            return html_writer::tag('a', $content, $attributes);
        }

        protected static function div($attributes, $content) {
            return html_writer::tag('div', $content, $attributes);
        }

        protected static function span($attributes, $content) {
            return html_writer::tag('span', $content, $attributes);
        }

        protected static function icon($name, $text=null) {
            if (!$text) {$text = $name;}
            return "<i class=icon-$name></i>&nbsp;";
        }
        protected static function moodle_icon($name) {
            return self::icon(self::$icons[$name]);
        }
        public function icon_help() {
            return self::icon('question-sign');
        }

        public function action_icon($url, pix_icon $pixicon, component_action $action = null, array $attributes = null, $linktext=false) {
            if (!($url instanceof moodle_url)) {
                $url = new moodle_url($url);
            }
            $attributes = (array)$attributes;

            if (empty($attributes['class'])) {
                // let ppl override the class via $options
                $attributes['class'] = 'action-icon';
            }

            $icon = $this->render($pixicon);

            if ($linktext) {
                $text = $pixicon->attributes['alt'];
            } else {
                $text = '';
            }

            return $this->action_link($url, $text.$icon, $action, $attributes);
        }

        protected function render_pix_icon(pix_icon $icon) {

            if (isset(self::$icons[$icon->pix])) {
                return self::icon(self::$icons[$icon->pix]);
            } else {
                //return parent::render_pix_icon($icon);
                return '<i class=icon-not-assigned data-debug-icon="'.$icon->pix.'"></i>';
            }


        }



}
