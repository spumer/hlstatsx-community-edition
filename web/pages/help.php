<?php
/*
HLstatsX Community Edition - Real-time player and clan rankings and statistics
Copyleft (L) 2008-20XX Nicholas Hastings (nshastings@gmail.com)
http://www.hlxcommunity.com

HLstatsX Community Edition is a continuation of 
ELstatsNEO - Real-time player and clan rankings and statistics
Copyleft (L) 2008-20XX Malte Bayer (steam@neo-soft.org)
http://ovrsized.neo-soft.org/

ELstatsNEO is an very improved & enhanced - so called Ultra-Humongus Edition of HLstatsX
HLstatsX - Real-time player and clan rankings and statistics for Half-Life 2
http://www.hlstatsx.com/
Copyright (C) 2005-2007 Tobias Oetzel (Tobi@hlstatsx.com)

HLstatsX is an enhanced version of HLstats made by Simon Garner
HLstats - Real-time player and clan rankings and statistics for Half-Life
http://sourceforge.net/projects/hlstats/
Copyright (C) 2001  Simon Garner
            
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

For support and installation notes visit http://www.hlxcommunity.com
*/

    if (!defined('IN_HLSTATS')) {
        die('Do not access this file directly.');
    }

	global $game;
	$resultGames = $db->query
	("
		SELECT
			hlstats_Games.code,
			hlstats_Games.name
		FROM
			hlstats_Games
		WHERE
			hlstats_Games.hidden = '0'
		ORDER BY
			hlstats_Games.name ASC 
		LIMIT
			0,
			1
	");
	list($game) = $db->fetch_row($resultGames);
// Help
	pageHeader
	(
		array (__('help.title')),
		array (__('help.title') => '')
	);
?>

<div class="block">
	<?php printSectionTitle(__('help.title.questions')); ?>
	<ol>
		<li>
			<a href="#players"><?=__('help.q.players')?></a><br />
		</li>
		<li>
			<a href="#points"><?=__('help.q.points')?></a><br />
		</li>
		<li>
			<a href="#weaponmods"><?=__('help.q.weaponmods')?></a><br />
		</li>
		<li>
			<a href="#set"><?=__('help.q.set')?></a><br />
		</li>
		<li>
			<a href="#hideranking"><?=__('help.q.hideranking')?></a>
		</li>
	</ol>

	<?php printSectionTitle(__('help.title.answers')); ?>

	<div style="margin-left:2%;">
		<h1 class="fTitle" style="padding-top:10px;"><a name="players">1. <?=__('help.q.players')?></a></h1><br /><br />
			<?php
				if ($g_options['Mode'] == 'NameTrack')
				{
			?>
			Players are tracked by nickname. All statistics for any player using a particular name will be grouped under that name. It is not possible for a name to be listed more than once for each game.<br /><br />
			<?php
				}
				else
				{
					if ($g_options['Mode'] == 'LAN')
					{
						$uniqueid = 'IP Address';
						$uniqueid_plural = 'IP Addresses';
			?>
			Players are tracked by IP Address. IP addresses are specific to a computer on a network.<br /><br />
			<?php
					}
					else
					{
						$uniqueid = 'Unique ID';
						$uniqueid_plural = 'Unique IDs';
			?>
			Players are tracked by Unique ID. Your Unique ID is the last two sections of your Steam ID (X:XXXX).<br /><br />
			<?php
					}
			?>
			<?=__('help.text.name_tracking')?><br /><br />
			<?=__('help.text.name_listed_pre')?><?php echo $uniqueid; ?><?=__('help.text.name_listed_post')?><br /><br />
			<?=__('help.text.search_pre')?><a href="<?php echo $g_options['scripturl']; ?>?mode=search"><?=__('help.link.search')?></a><?=__('help.text.search_mid')?><?php echo $uniqueid; ?><?=__('help.text.search_post')?><br /><br />
			<?php
				}
			?>
			<h1 class="fTitle" style="padding-top:10px;"><a name="points">2. <?=__('help.q.points')?></a></h1><br /><br />
			<?=__('help.text.points_gain')?><br /><br />
			<?=__('help.text.points_lose')?><br /><br />
			<?=__('help.text.equations_intro')?><br /><br />
			<pre> <?=__('help.formula.killer')?>
				 &times; <?=__('help.formula.weapon_modifier')?> &times; 5

 <?=__('help.formula.victim')?>
				 &times; <?=__('help.formula.weapon_modifier')?> &times; 5</pre><br /><br />
			<?=__('help.text.point_bonuses_intro')?><br /><br />
			<a name="actions" />
			<?php
				$tblActions = new Table
				(
					array
					(
						new TableColumn
						(
							'gamename',
							__('help.col.game'),
							'width=24&sort=no'
						),
						new TableColumn
						(
							'for_PlayerActions',
							__('help.col.player_action'),
							'width=4&sort=no&align=center'
						),
						new TableColumn
						(
							'for_PlayerPlayerActions',
							__('help.col.plyrplyr_action'),
							'width=4&sort=no&align=center'
						),
						new TableColumn
						(
							'for_TeamActions',
							__('help.col.team_action'),
							'width=4&sort=no&align=center'
						),
						new TableColumn
						(
							'for_WorldActions',
							__('help.col.world_action'),
							'width=4&sort=no&align=center'
						),
						new TableColumn
						(
							'description',
							__('claninfo_actions.col.action'),
							'width=33'
						),
						new TableColumn
						(
							's_reward_player',
							__('help.col.player_reward'),
							'width=12'
						),
						new TableColumn
						(
							's_reward_team',
							__('help.col.team_reward'),
							'width=15'
						)
					),
					'id',
					'description',
					's_reward_player',
					false,
					9999,
					'act_page',
					'act_sort',
					'act_sortorder',
					'actions',
					'asc'
				);
				$result = $db->query
				("
					SELECT
						hlstats_Games.name AS gamename,
						hlstats_Actions.description,
						IF(SIGN(hlstats_Actions.reward_player) > 0, CONCAT('+', hlstats_Actions.reward_player), hlstats_Actions.reward_player) AS s_reward_player,
						IF(hlstats_Actions.team != '' AND hlstats_Actions.reward_team != 0,
						IF(SIGN(hlstats_Actions.reward_team) >= 0, CONCAT(hlstats_Teams.name, ' +', hlstats_Actions.reward_team), CONCAT(hlstats_Teams.name, ' ', hlstats_Actions.reward_team)), '') AS s_reward_team,
						IF(for_PlayerActions='1', 'Yes', 'No') AS for_PlayerActions,
						IF(for_PlayerPlayerActions='1', 'Yes', 'No') AS for_PlayerPlayerActions,
						IF(for_TeamActions='1', 'Yes', 'No') AS for_TeamActions,
						IF(for_WorldActions='1', 'Yes', 'No') AS for_WorldActions
					FROM
						hlstats_Actions
					INNER JOIN
						hlstats_Games
					ON
						hlstats_Games.code = hlstats_Actions.game
						AND hlstats_Games.hidden = '0'
					LEFT JOIN
						hlstats_Teams
					ON
						hlstats_Teams.code = hlstats_Actions.team
						AND hlstats_Teams.game = hlstats_Actions.game
					ORDER BY
						hlstats_Actions.game ASC,
						$tblActions->sort $tblActions->sortorder,
						$tblActions->sort2 $tblActions->sortorder
				");
				$numitems = $db->num_rows($result);
				$tblActions->draw($result, $numitems, 90, 'center');
			?><br /><br />
			<strong><?=__('help.label.note')?></strong> <?=__('help.text.action_reward_note')?><br /><br />
			<h1 class="fTitle" style="padding-top:10px;"><a name="weaponmods">3. <?=__('help.q.weaponmods')?></a></h1><br /><br />
			<?=__('help.text.weapon_modifiers')?><br /><br />
			<a name="weapons"></a>
			<?php
				$tblWeapons = new Table
				(
					array
					(
						new TableColumn
						(
							'gamename',
							__('help.col.game'),
							'width=24&sort=no'
						),
						new TableColumn
						(
							'code',
							__('claninfo_weapons.col.weapon'),
							'width=14'
						),
						new TableColumn
						(
							'name',
							__('common.col.name'),
							'width=50'
						),
						new TableColumn
						(
							'modifier',
							__('claninfo_weapons.col.points_modifier'),
							'width=12'
						)
					),
					'weaponId',
					'modifier',
					'code',
					false,
					9999,
					'weap_page',
					'weap_sort',
					'weap_sortorder',
					'weapons',
					'desc'
				);
				$result = $db->query
				("
					SELECT
						hlstats_Games.name AS gamename,
						hlstats_Weapons.code,
						hlstats_Weapons.name,
						hlstats_Weapons.modifier
					FROM
						hlstats_Weapons
					INNER JOIN
						hlstats_Games
					ON
						hlstats_Games.code = hlstats_Weapons.game
						AND hlstats_Games.hidden = '0'
					ORDER BY
						hlstats_Weapons.game ASC,
						$tblWeapons->sort $tblWeapons->sortorder,
						$tblWeapons->sort2 $tblWeapons->sortorder
				");
				$numitems = $db->num_rows($result);
				$tblWeapons->draw($result, $numitems, 90, "center");
			?><br /><br />
			<h1 class="fTitle" style="padding-top:10px;"><a name="set">4. <?=__('help.q.set')?></a></h1><br /><br />
			<?=__('help.text.set_intro')?><br /><br />
			<?=__('help.text.set_syntax')?><br /><br />
			<?=__('help.text.set_options_intro')?>
			<ul>
				<li><strong>realname</strong><br />
					<?=__('help.text.set_realname')?><br />
					<?=__('help.label.example')?> &nbsp; <strong>/hlx_set realname Joe Bloggs</strong><br /><br />
				</li>
			
				<li><strong>email</strong><br />
					<?=__('help.text.set_email')?><br />
					<?=__('help.label.example')?> &nbsp; <strong>/hlx_set email joe@joebloggs.com</strong><br /><br />
				</li>
				
				<li><strong>homepage</strong><br />
					<?=__('help.text.set_homepage')?><br />
					<?=__('help.label.example')?> &nbsp; <strong>/hlx_set homepage http://www.joebloggs.com/</strong><br /><br />
				</li>
			</ul>
			<strong><?=__('help.label.note')?></strong> <?=__('help.text.set_note')?><br /><br />
			<h1 class="fTitle" style="padding-top:10px;"><a name="hideranking">5. <?=__('help.q.hideranking')?></a></h1><br /><br />
			<?=__('help.text.hideranking')?><br /><br />
			<strong><?=__('help.label.note')?></strong> <?=__('help.text.hideranking_note_pre')?><a href="<?php echo $g_options['scripturl']; ?>?mode=search"><?=__('help.link.search')?></a><?=__('help.text.hideranking_note_post')?>
	</div>
</div>
