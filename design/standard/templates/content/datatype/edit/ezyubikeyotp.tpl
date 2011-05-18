{literal}
<script language=javascript type=text/javascript>
<!-- Script courtesy of http://www.web-source.net - Your Guide to Professional Web Site Design and Development
function stopRKey(evt) {
   var evt = (evt) ? evt : ((event) ? event : null);
   var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
   if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
}

document.onkeypress = stopRKey;
-->
</script>
{/literal}
<input type="text" size="10"
      name="ContentObjectAttribute_data_text_{$attribute.id}"
      value="{$attribute.data_text}" class="yubiKeyInput" />
