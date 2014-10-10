{* template block that contains the new field *}
<div id="testfield-tr">
  <div>Used for permissions: {$form.perms_add.html}</div>
</div>
{* reposition the above block after #someOtherBlock *}
<script type="text/javascript">
  cj('#testfield-tr').insertAfter('td:contains("Use another contact\'s address")');
</script>
