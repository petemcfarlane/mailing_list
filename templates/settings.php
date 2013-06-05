<form id="mailing_list" method="post">
	<fieldset class="personalblock">
		<legend>Mailing Lists</legend>
		<table>
			<thead>
				<tr>
					<th>Mailing List</th><th>Number of Members</th><th></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$query = OC_DB::prepare('SELECT * FROM *PREFIX*mailing_lists');
				$result = $query->execute();
				$lists = $result->fetchAll();
				?>
				<?php foreach ($lists as $list) { ?>
					<tr class="member_list" data-list-id="<?php p($list['mailing_list_id']); ?>">
						<td><input type="text" name="mailing_list_id_<?php p($list['mailing_list_id']); ?>" value="<?php p($list['mailing_list_name']); ?>" class="mailing_list_names" /></td>
						<td><?php p($list['members']); ?></td>
						<td><a href="#" class="remove_mailing_list" data-mailing_list_id="<?php p($list['mailing_list_id']); ?>" title="Members will still exist, but won't be part of this list anymore">
							<img src="<?php p(OCP\Util::imagePath('mailing_list', 'delete.svg' )); ?>">
						</a></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<label for="add_mailing_list_name">Create List: </label>
		<input type="text" name="add_mailing_list_name" id="add_mailing_list_name" placeholder="e.g. 'General'"/>
		<input type="submit" name="add_mailing_list" value="Save" />
		<span class="msg"></span>
	</fieldset>
</form>
