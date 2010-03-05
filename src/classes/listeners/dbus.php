<?php
/**
 * TwIRCd - Twitter IRC Server
 *
 * This file is part of TwIRCd.
 *
 * TwIRCd is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * TwIRCd is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TwIRCd; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Listeners
 * @version $Revision$
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace TwIRCd\Listeners;

class Dbus
{
    protected function notify($title, $message)
    {
        if (!class_exists('Dbus', false)) {
            return;
        }

        $d = new \Dbus(\Dbus::BUS_SESSION);

        $n = $d->createProxy(
                'org.freedesktop.Notifications',
                '/org/freedesktop/Notifications',
                'org.freedesktop.Notifications'
        );

        $n->Notify(
            'Twircd_Util_DbusLog', new \DBusUInt32(0),
            'twircd', "TwIRCd: ".$title, $message,
            new \DBusArray(\DBus::STRING, array()),
            new \DBusDict(\DBus::VARIANT, array()),
            1500
        );
    }

    /**
     *
     * @param  array $messages
     * @return void
     */
    public function onUpdates($messages)
    {
        if (count($messages) > 0) {
            $users = array();
            foreach ($messages AS $message) {
                $users[] = array_shift( explode( "!",  $message->from ) );
            }
            $users = array_unique($users);

            $message = "From:\n -".implode("\n-", $users);

            $this->notify(count($messages)." new tweets", $message);
        }
    }
}