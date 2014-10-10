<h3 style="border: 2px solid #4CAE4C; background-color: white; color: #428BCA; border-radius: 5px; text-align:center;">
{ts domain='org.ndi.civimp'}New Contacts by Month{/ts}</div>
<ul class="list-group">
{foreach from=$createdArray key=name item=contactsCreated}
  <li class="list-group-item" id="{$name}"><span style=" font-weight: bold;">{$contactsCreated}</span></li>
<script type="text/javascript">
{literal}
cj(function($){
{/literal}
  var contactsCreated = {$contactsCreated};
  var name = '{$name}';
  var contactsCreatedAgg = {$createdAgg[$name]};
{literal}
  for (i=0; i<contactsCreatedAgg; i++)
  {
    $('#'+name).append(' <span style="color: #428BCA;" class="glyphicon glyphicon-user"></span>');
  }
});
{/literal}
</script>
{/foreach}
</ul>
