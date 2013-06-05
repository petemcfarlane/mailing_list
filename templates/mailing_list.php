<?php 
$query = OC_DB::prepare('SELECT * FROM *PREFIX*mailing_list');
$result = $query->execute();
$members = $result->fetchAll();

$query = OC_DB::prepare('SELECT * FROM *PREFIX*mailing_lists');
$result = $query->execute();
$mailing_lists = $result->fetchAll();
$lists = array();
foreach ($mailing_lists as $mailing_list) {
	$lists[$mailing_list['mailing_list_id']] = $mailing_list['mailing_list_name'];
}
?>
<div id="controls">
	
	<form method="post" action="" id="new_member_form">
		<input id="new_member_name" type="text" maxlength="100" name="member_name" placeholder="Name" />
		<input id="new_member_email" type="email" maxlength="100" name="member_email" placeholder="Email" />
		<select	class="member_lists" data-placeholder="groups" title="Select lists" multiple="multiple" name="member_lists[]">
			<?php foreach($lists as $list_id => $list_name) { ?>
				<option><?php p( $list_name ); ?></option>
			<?php } ?>
		</select> 
		<input type="submit" name="new_member" value="Add Member" />
		<span class="msg"></span>
	</form>
	
	<form action='/apps/members/ajax/upload_vcards.php' id="upload_vcf_form" enctype="multipart/form-data" method="post">
		<label for="import_from_vcf" class="tag">Import from VCF</label>
		<select	class="member_lists" data-placeholder="groups" title="Select lists" multiple="multiple" name="member_lists[]">
			<?php foreach($lists as $list_id => $list_name) { ?>
				<option><?php p( $list_name ); ?></option>
			<?php } ?>
		</select> 
		<input id="import_from_vcf" class="hidden" type="file" name="import_file" accept="text/vcard,text/x-vcard,text/directory" />
		<input type="submit" value="Import" />
	</form>

</div>

<table id="members_list">
	<thead>
		<tr>
			<th>Name</th><th>Email</th><th>Lists</th><th>Member Since</th><th>IP Address</th><th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($members as $member) { ?>
			<tr class="contact">
				<td>
					<?php p( $member['member_name'] ); ?>
				</td>
				<td>
					<?php p( $member['member_email'] ); ?>
				</td>
				<td>
					<?php $member_lists = explode(',', $member['member_mailing_lists'], -1); ?>
					<select class="member_lists" title="+ Member Lists" multiple="multiple" data-member_id="<?php p( $member['member_id'] ); ?>">
						<?php foreach ($lists as $list => $name) {
							$selected = in_array($name,$member_lists) ? ' selected="selected"' : ''; ?>
							<option<?php p($selected); ?>><?php p($name); ?></option>
						<?php } ?>
					</select>
				</td>
				<td>
					<?php p( date("j/m/Y", strtotime($member['member_since'])) ); ?>
				</td>
				<td>
					<a href="http://who.is/whois-ip/ip-address/<?php p( $member['ip_address'] ); ?>" target="_blank"><?php p( $member['ip_address'] ); ?></a>
				</td>
				<td class="remove">
					<a href="#" class="action delete" original-title="Delete" data-member_id="<?php p( $member['member_id'] ); ?>">
						<img src="<?php p(OCP\Util::imagePath('mailing_list', 'delete.svg' )); ?>">
					</a>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>

