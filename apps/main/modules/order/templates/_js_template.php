<script type="text/html" id="moveitem_tmpl">
  <span class="bButtonPopup" style="left: 203px">
    <span class="bButtonPopup__eTitle">Переместить товар:</span>
    <% for ( var i = 0; i < dlvr . length; i++ ) { %>
      <a class="bButtonPopup__eLine moveline"><%=dlvr[i].title%></a>
    <% } %>
  </span>
</script>

<script type="text/html" id="tip_tmpl">
  <span class="bTooltip" style="top:-38px; left:<%=cssl%>">
    <span class="bTooltip__eText"><%=tiptext%></span>
    <span class="bTooltip__eArrow"></span>
  </span>
</script>
