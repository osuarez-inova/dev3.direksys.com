<?php
/**
 * Description
 *
 * @author L.C.I Cesar Cedillo
 */
session_start();

include_once '../../trsBase.php';
include_once '../../session.php';

$COMMON_PATH = "../..";

unset($_SESSION['ARRAY_PO_PARTS']);

//-- parametros validos para la accion
/*
 * : chg_total  => Cambio total por lo que viene en la orden
 * : chg_fisico => Cambio por otra mercancia que el cliente elige
 *
 */

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link href="<?php echo $COMMON_PATH ?>/common/css/main.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $COMMON_PATH ?>/common/css/common.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $COMMON_PATH ?>/common/js/jquery.msgbox.css" type="text/css" rel="stylesheet"/>
        <link href="<?php echo $COMMON_PATH ?>/common/js/css/smoothness/jquery-ui-1.8.17.custom.css" type="text/css" rel="stylesheet"/>
        <link href="<?php echo $COMMON_PATH ?>/common/js/css/simplemodal.css" type="text/css" rel="stylesheet" media="screen" />
        <script type="text/javascript" src="<?php echo $COMMON_PATH ?>/common/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="<?php echo $COMMON_PATH ?>/common/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo $COMMON_PATH ?>/common/js/jquery.ui.datepicker-es.js"></script>
        <script type="text/javascript" src="<?php echo $COMMON_PATH ?>/common/js/jquery.msgbox.min.js"></script>
        <script type="text/javascript" src="<?php echo $COMMON_PATH ?>/common/js/validaciones.js"></script>
        <script type="text/javascript" src="<?php echo $COMMON_PATH ?>/common/js/jquery.simplemodal.1.4.3.min.js"></script>

        <script type="text/javascript">
            var return_type = "<?php echo $return_type ?>";

            function changeHeaderModName(){

                var module_name = "Recepcion de Mercancia";
                module_name += " > " + "Purchase Orders";

                var module_name = $("#spn-mod-name").html(module_name);
            }


            $(document).ready(function(){
                $("#popup-vendors").hide();
                resetSearchPopUp('resVendorSearch','vendor-table-results');

                var id_popup1 = "popup-parts1";

                //createPopUpItems(id_popup1, "Items de la Orden", "yes", "main-container","orders");
                createPopUpItems(id_popup1, "Items de la Orden de Compra", "yes", "main-container","po");

                $("#"+id_popup1).hide();

                changeHeaderModName();
            });

            function resetSearchPopUp(idBtnReset,idTableResults){
                $("#"+ idBtnReset).click();
                $("#"+ idTableResults).html("");
            }

            function triggerPopupVendors(){
                resetSearchPopUp('resVendorSearch','vendor-table-results');

                $("#popup-vendors").modal({
                    position: ['40',]
                });

                $("#popup-vendors").show();
            }

            function createPopUpItems(idPopup, popupTitle, isFirst, targetContainer,targetUsage){
                var ajax_url = "<?php echo $COMMON_PATH ?>" + "/common/php/popup/ajax/ajax_popup_parts_generator.php";;
                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data: {
                        id_popup: idPopup,
                        popup_title : popupTitle,
                        first_popup : isFirst,
                        target_usage : targetUsage
                    },
                    success: function(data){
                        $("#" + targetContainer).append(data);
                        $("#" + idPopup).hide();
                    }
                });
            }

            function triggerPopupParts(idPopup, top, left, relatedPartID){
                resetSearchPopUp(idPopup);
                $("#"+idPopup).modal({
                    onShow: function(dialog){
                        $("#hdRelatedPartID-"+idPopup).val(relatedPartID);
                    },
                    position: [top ,]
                });
            }

            function resetSearchPopUp(idPopup){
                $("#frm-" + idPopup).click();
                $("#"+ idPopup + "-table-results").html("");
            }


            $(function() {
                $("#txtPaymentDate").datepicker(
                {
                    dateFormat: "yy-mm-dd",
                    buttonImage: '<?php echo $COMMON_PATH; ?>/common/img/calendar.gif',
                    buttonImageOnly: true,
                    showOn: 'button'
                }
            );

                $("#txtPODate").datepicker(
                {
                    dateFormat: "yy-mm-dd",
                    buttonImage: '<?php echo $COMMON_PATH; ?>/common/img/calendar.gif',
                    buttonImageOnly: true,
                    showOn: 'button'
                }
            );
                $("#txtPOEtd").datepicker(
                {
                    dateFormat: "yy-mm-dd",
                    buttonImage: '<?php echo $COMMON_PATH; ?>/common/img/calendar.gif',
                    buttonImageOnly: true,
                    showOn: 'button'
                }
                
                
            );
            });

        </script>
        <script type="text/javascript">
            /**************************************************************************/
            /* SCRIPTS Y FUNCIONES PARA LA MANIPULACION DE LAS PARTS*/
            /**************************************************************************/

            /*
            Function: ajaxSelectPart

            Selecciona una Part del listado de busqueda y la agrega a la tabla en pantalla.

            Parameters:

               targetUsage - Uso que se le dar� en la pagina en caso de que se requiera mas de 1 listado en la misma pagina..
               idPart - Identificador de la parte.
               skuPart - SKU de la parte.
               partModel - Modelo de pa parte.
               partName - Nombre de la parte.
               partCost - Costo de la parte.

            Returns:

               Nothing.

            See Also:

               /common/php/popup/ajax/ajax_popup_parts_generator.php
             */

            var SIZE_PRODUCT_LIST = 0;
            var SIZE_EXCHANGE_LIST = 0;

            function ajaxSelectPart(targetUsage,idPart,skuPart,partModel,partName,partCost, relatedPartID){

                var ajax_url = "";
                var ajax_response_container = "";

                if(parseFloat(partCost) < 0){
                    partCost = 0.00;    //-- costo por default
                }



                if(targetUsage == 'po'){
                    //ajaxAddOrderPart(idPart,skuPart,partModel,partName,partCost);
                    ajax_url = "ajax/ajax_po_items_control.php";
                    ajax_response_container = "div-order-products";
                }

                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data: {
                        pid: idPart,
                        psku : skuPart,
                        pmodel : partModel,
                        pname : partName,
                        pcost: partCost,
                        relpidx : relatedPartID,
                        action: 'add_part'
                    },
                    success: function(data){
                        if(targetUsage == 'cod'){
                            ajaxUpdateOrderTotals();
                        }
                        $("#" + ajax_response_container).html(data);

                    }
                });
            }

            function ajaxDeleteOrderPart(index){
                $.msgbox("Desea quitar el producto de la lista?", {
                    type: "confirm",
                    buttons : [
                        {type: "submit", value: "Yes"},
                        {type: "cancel", value: "Cancel"}
                    ]
                }, function(result) {
                    if(result == "Yes"){
                        var ajax_url = "ajax/ajax_order_items_control_undelivered.php";
                        $.ajax({
                            type: 'POST',
                            url: ajax_url,
                            data: {
                                idx: index,
                                action: 'del_part'
                            },
                            success: function(data){
                                if(SIZE_EXCHANGE_LIST > 0){
                                    refreshExchangeList();
                                    ajaxUpdateExchangeTotals();
                                }
                                ajaxUpdateOrderTotals();
                                $("#div-order-products").html(data);
                            }
                        });
                    }
                });
            }


            function ajaxDeleteExchangePart(index, index_exchg){
                $.msgbox("Desea quitar el producto de la lista?", {
                    type: "confirm",
                    buttons : [
                        {type: "submit", value: "Yes"},
                        {type: "cancel", value: "Cancel"}
                    ]
                }, function(result) {
                    if(result == "Yes"){
                        var ajax_url = "ajax/ajax_exchange_items_control.php";
                        $.ajax({
                            type: 'POST',
                            url: ajax_url,
                            data: {
                                idx: index,
                                relpidx: index,
                                exchgidx: index_exchg,
                                action: 'del_part'
                            },
                            success: function(data){
                                ajaxUpdateExchangeTotals();
                                $("#div-exchange-products").html(data);
                            }
                        });
                    }
                });
            }

            function ajaxUpdateProductInformation(idx){
                var ajax_url = "ajax/ajax_order_items_control_undelivered.php";

                var product_qty = $("#txtOrdProductQty-"+idx).val();
                var product_sprice = $("#txtOrdProductSPrice-"+idx).val();
                var product_discount = $("#txtOrdProductDisc-"+idx).val();
                var product_shipping = $("#txtOrdProductShipping-"+idx).val();
                var product_cost = $("#txtOrdProductCost-"+idx).val();
                var product_tax = $("#txtOrdProductTax-"+idx).val();
                var product_total = $("#txtOrdProductTotal-"+idx).val();
                var product_upc_status = $("#lstUpcStatus-"+idx).val();
                /*
                var product_pkg_condition = $("#lstProductPkgCondition-"+idx).val();
                var product_item_condition = $("#lstItemCondition-"+idx).val();
                 */

                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data: {
                        idx: idx,
                        pqty: product_qty,
                        pcost : product_cost,
                        ptotal: product_total,
                        psprice : product_sprice,
                        pdiscount : product_discount,
                        pship : product_shipping,
                        ptax : product_tax,
                        /* ppckgcond : product_pkg_condition,
                        pitemcond : product_item_condition, */
                        pupcstatus : product_upc_status,
                        action: 'mod_part_info'
                    },
                    success: function(data){
                        /*
                        if(data == 0){
                            $("#spnLegendUpcs-"+idx).css({'color':'#000000'});
                            $("#hdHasUpcs").val('');
                        }else if(data == 1){
                            $("#spnLegendUpcs-"+idx).css({'color':'#D31E11'});
                            $("#hdHasUpcs").val('');
                        }else if(data == 2){
                            $("#spnLegendUpcs-"+idx).css({'color':'#000000'});
                        }
                         */
                        ajaxUpdateOrderTotals();
                    }
                });
            }


            function ajaxUpdateProductExchangeInformation(idx, idxex){
                var ajax_url = "ajax/ajax_exchange_items_control.php";

                var product_qty = $("#txtOrdProductQty-"+idx + "-" + idxex).val();
                var product_sprice = $("#txtOrdProductSPrice-"+idx + "-" + idxex).val();
                var product_discount = $("#txtOrdProductDisc-"+idx + "-" + idxex).val();
                var product_shipping = $("#txtOrdProductShipping-"+idx + "-" + idxex).val();
                var product_cost = $("#txtOrdProductCost-"+idx + "-" + idxex).val();
                var product_total = $("#txtOrdProductTotal-"+idx + "-" + idxex).val();
                var product_tax = $("#txtOrdProductTax-"+idx + "-" + idxex).val();

                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data: {
                        idx: idx,
                        relpidx: idxex,
                        pqty: product_qty,
                        pcost : product_cost,
                        ptotal: product_total,
                        psprice : product_sprice,
                        pdiscount : product_discount,
                        pship : product_shipping,
                        ptax : product_tax,
                        action: 'mod_part_info'
                    },
                    success: function(data){
                        ajaxUpdateExchangeTotals();
                    }
                });
            }

            function ajaxUpdateOrderTotals(){
                var ajax_url = "ajax/ajax_order_items_control_undelivered.php";
                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data: {
                        action: 'update_order_totals'
                    },
                    success: function(data){
                        if(data != ''){
                            var obJson = eval( "(" + data + ")" );

                            $("#spnOrderItemsTotal").html(obJson ['total_items']);
                            $("#spnOrderItemsTotalShip").html(obJson ['total_ship']);
                            $("#spnOrderItemsTotalDisc").html(obJson ['total_disc']);
                            $("#spnOrderItemsTotalTax").html(obJson ['total_tax']);
                            $("#spnOrderTotal").html(obJson ['total_order']);

                            SIZE_PRODUCT_LIST = obJson ['total_product_list']

                            $("#div-order-totals").css({'display':'block'});
                        }else{
                            $("#div-order-totals").css({'display':'none'});
                        }
                    }
                });
            }


            function ajaxUpdateExchangeTotals(){
                var ajax_url = "ajax/ajax_exchange_items_control.php";
                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data: {
                        action: 'update_exchange_totals'
                    },
                    success: function(data){
                        if(data != ''){
                            var obJson = eval( "(" + data + ")" );

                            $("#spnExchangeItemsTotal").html(obJson ['total_items']);
                            $("#spnExchangeItemsTotalShip").html(obJson ['total_ship']);
                            $("#spnExchangeItemsTotalDisc").html(obJson ['total_disc']);
                            $("#spnExchangeItemsTotalTax").html(obJson ['total_tax']);
                            $("#spnExchangeTotal").html(obJson ['total_order']);

                            SIZE_EXCHANGE_LIST = obJson ['total_product_list']

                            $("#div-exchange-totals").css({'display':'block'});
                        }else{
                            $("#div-exchange-totals").css({'display':'none'});
                        }
                    }
                });
            }
            //-----------------------------------------------------------------//


            function validateOrderProductInformation(idx){

                var success= false;
                var error_msg = "";
                var txtOrdProductTotal = 0;

                var product_qty = $("#txtOrdProductQty-"+idx).val();
                var product_sprice = $("#txtOrdProductSPrice-"+idx).val();
                var product_discount = $("#txtOrdProductDisc-"+idx).val();
                var product_shipping = $("#txtOrdProductShipping-"+idx).val();
                var product_cost = $("#txtOrdProductCost-"+idx).val();
                var product_tax = $("#txtOrdProductTax-"+idx).val();

                if(validar_flotante(product_qty)){
                    if(parseFloat(product_qty) <= 0){
                        error_msg += "- La cantidad debe ser mayor a 0.<br>";
                        product_qty = 1;
                        $("#txtOrdProductQty-" + idx).val(product_qty);
                        $("#txtOrdProductQty-" + idx).focus();
                    }
                }else{
                    error_msg += "- La cantidad debe ser un numero v&aacute;lido.<br>";
                    product_qty = 1;
                    $("#txtOrdProductQty-" + idx).val(product_qty);
                    $("#txtOrdProductQty-" + idx).focus();
                }

                if(validar_flotante(product_sprice)){
                    if(parseFloat(product_sprice) <= 0){
                        error_msg += "- El precio de venta debe ser mayor a 0.<br>";
                        product_sprice = 0.01;
                        $("#txtOrdProductSPrice-" + idx).val(product_sprice);
                        $("#txtOrdProductSPrice-" + idx).focus();
                    }
                }else{
                    error_msg += "- El precio de venta debe ser un n&uacute;mero v&aacute;lido.<br>";
                    product_sprice = 0.01;
                    $("#txtOrdProductSPrice-" + idx).val(product_sprice);
                    $("#txtOrdProductSPrice-" + idx).focus();
                }


                if(validar_flotante(product_discount)){
                    if(parseFloat(product_discount) < 0){
                        error_msg += "- El descuento debe ser mayor o igual a 0.<br>";
                        product_discount = 0.00;
                        $("#txtOrdProductDisc-" + idx).val(product_discount);
                        $("#txtOrdProductDisc-" + idx).focus();
                    }
                }else{
                    error_msg += "- El descuento debe ser un n&uacute;mero v&aacute;lido.<br>";
                    product_discount = 0.00;
                    $("#txtOrdProductDisc-" + idx).val(product_discount);
                    $("#txtOrdProductDisc-" + idx).focus();
                }

                if(validar_flotante(product_shipping)){
                    if(parseFloat(product_shipping) < 0){
                        error_msg += "- El costo del envio debe ser mayor o igual a 0.<br>";
                        $("#txtOrdProductShipping-" + idx).val("0.00");
                        $("#txtOrdProductShipping-" + idx).focus();
                    }
                }else{
                    error_msg += "- El costo del envio debe ser un n&uacute;mero v&aacute;lido.<br>";
                    $("#txtOrdProductShipping-" + idx).val("0.00");
                    $("#txtOrdProductShipping-" + idx).focus();
                }

                if(validar_flotante(product_cost)){
                    if(parseFloat(product_cost) < 0){
                        error_msg += "- El costo del item debe ser mayor o igual a 0.<br>";
                        $("#txtOrdProductCost-" + idx).val("0.00");
                        $("#txtOrdProductCost-" + idx).focus();
                    }
                }else{
                    error_msg += "- El costo del item debe ser un n&uacute;mero v&aacute;lido.<br>";
                    $("#txtOrdProductCost-" + idx).val("0.00");
                    $("#txtOrdProductCost-" + idx).focus();
                }

                if(validar_flotante(product_tax)){
                    if(parseFloat(product_tax) < 0){
                        error_msg += "- El tax del item debe ser mayor o igual a 0.<br>";
                        $("#txtOrdProductCost-" + idx).val("0.00");
                        $("#txtOrdProductCost-" + idx).focus();
                    }
                }else{
                    error_msg += "- El tax del item debe ser un n&uacute;mero v&aacute;lido.<br>";
                    $("#txtOrdProductTax-" + idx).val("0.00");
                    $("#txtOrdProductTax-" + idx).focus();
                }

                txtOrdProductTotal = calculateProductTotal(product_qty,product_sprice);
                $("#txtOrdProductTotal-" + idx).val(txtOrdProductTotal);

                if(error_msg == ""){
                    txtOrdProductTotal = calculateProductTotal(product_qty,product_sprice);
                    $("#txtOrdProductTotal-" + idx).val(txtOrdProductTotal);
                    ajaxUpdateProductInformation(idx);
                    success = true;
                }else{
                    $.msgbox("Se encontraron los siguientes errores:<br><br>" + error_msg, {
                        buttons : [
                            {type: "submit", value: "Ok"}
                        ]
                    },function(result) {
                        txtOrdProductTotal = calculateProductTotal(product_qty,product_sprice);
                        $("#txtOrdProductTotal-" + idx).val(txtOrdProductTotal);
                        ajaxUpdateProductInformation(idx);
                    });
                }
                return success;
            }


            function validateExchangeProductInformation(idx,idxex){

                var success= false;
                var error_msg = "";
                var txtOrdProductTotal = 0;

                var product_qty = $("#txtOrdProductQty-"+idx + "-" + idxex).val();
                var product_sprice = $("#txtOrdProductSPrice-"+idx + "-" + idxex).val();
                var product_discount = $("#txtOrdProductDisc-"+idx + "-" + idxex).val();
                var product_shipping = $("#txtOrdProductShipping-"+idx + "-" + idxex).val();
                var product_cost = $("#txtOrdProductCost-"+idx + "-" + idxex).val();
                var product_tax = $("#txtOrdProductTax-"+idx + "-" + idxex).val();

                if(validar_flotante(product_qty)){
                    if(parseFloat(product_qty) <= 0){
                        error_msg += "- La cantidad debe ser mayor a 0.<br>";
                        product_qty = 1;
                        $("#txtOrdProductQty-" + idx + "-" + idxex).val(product_qty);
                        $("#txtOrdProductQty-" + idx + "-" + idxex).focus();
                    }
                }else{
                    error_msg += "- La cantidad debe ser un numero v&aacute;lido.<br>";
                    product_qty = 1;
                    $("#txtOrdProductQty-" + idx + "-" + idxex).val(product_qty);
                    $("#txtOrdProductQty-" + idx + "-" + idxex).focus();
                }

                if(validar_flotante(product_sprice)){
                    if(parseFloat(product_sprice) <= 0){
                        error_msg += "- El precio de venta debe ser mayor a 0.<br>";
                        product_sprice = 0.01;
                        $("#txtOrdProductSPrice-" + idx + "-" + idxex).val(product_sprice);
                        $("#txtOrdProductSPrice-" + idx + "-" + idxex).focus();
                    }
                }else{
                    error_msg += "- El precio de venta debe ser un n&uacute;mero v&aacute;lido.<br>";
                    product_sprice = 0.01;
                    $("#txtOrdProductSPrice-" + idx + "-" + idxex).val(product_sprice);
                    $("#txtOrdProductSPrice-" + idx + "-" + idxex).focus();
                }


                if(validar_flotante(product_discount)){
                    if(parseFloat(product_discount) < 0){
                        error_msg += "- El descuento debe ser mayor o igual a 0.<br>";
                        product_discount = 0.00;
                        $("#txtOrdProductDisc-" + idx + "-" + idxex).val(product_discount);
                        $("#txtOrdProductDisc-" + idx + "-" + idxex).focus();
                    }
                }else{
                    error_msg += "- El descuento debe ser un n&uacute;mero v&aacute;lido.<br>";
                    product_discount = 0.00;
                    $("#txtOrdProductDisc-" + idx + "-" + idxex).val(product_discount);
                    $("#txtOrdProductDisc-" + idx + "-" + idxex).focus();
                }

                if(validar_flotante(product_shipping)){
                    if(parseFloat(product_shipping) < 0){
                        error_msg += "- El costo del envio debe ser mayor o igual a 0.<br>";
                        $("#txtOrdProductShipping-" + idx + "-" + idxex).val("0.00");
                        $("#txtOrdProductShipping-" + idx + "-" + idxex).focus();
                    }
                }else{
                    error_msg += "- El costo del envio debe ser un n&uacute;mero v&aacute;lido.<br>";
                    $("#txtOrdProductShipping-" + idx + "-" + idxex).val("0.00");
                    $("#txtOrdProductShipping-" + idx + "-" + idxex).focus();
                }

                if(validar_flotante(product_cost)){
                    if(parseFloat(product_cost) < 0){
                        error_msg += "- El costo del item debe ser mayor o igual a 0.<br>";
                        $("#txtOrdProductCost-" + idx + "-" + idxex).val("0.00");
                        $("#txtOrdProductCost-" + idx + "-" + idxex).focus();
                    }
                }else{
                    error_msg += "- El costo del item debe ser un n&uacute;mero v&aacute;lido.<br>";
                    $("#txtOrdProductCost-" + idx + "-" + idxex).val("0.00");
                    $("#txtOrdProductCost-" + idx + "-" + idxex).focus();
                }

                if(validar_flotante(product_tax)){
                    if(parseFloat(product_tax) < 0){
                        error_msg += "- El tax del item debe ser mayor o igual a 0.<br>";
                        $("#txtOrdProductCost-" + idx + "-" + idxex).val("0.00");
                        $("#txtOrdProductCost-" + idx + "-" + idxex).focus();
                    }
                }else{
                    error_msg += "- El tax del item debe ser un n&uacute;mero v&aacute;lido.<br>";
                    $("#txtOrdProductTax-" + idx + "-" + idxex).val("0.00");
                    $("#txtOrdProductTax-" + idx + "-" + idxex).focus();
                }

                txtOrdProductTotal = calculateProductTotal(product_qty,product_sprice);
                $("#txtOrdProductTotal-" + idx + "-" + idxex).val(txtOrdProductTotal);

                if(error_msg == ""){
                    txtOrdProductTotal = calculateProductTotal(product_qty,product_sprice);
                    $("#txtOrdProductTotal-" + idx + "-" + idxex).val(txtOrdProductTotal);
                    ajaxUpdateProductExchangeInformation(idx,idxex);
                    success = true;
                }else{
                    $.msgbox("Se encontraron los siguientes errores:<br><br>" + error_msg, {
                        buttons : [
                            {type: "submit", value: "Ok"}
                        ]
                    },function(result) {
                        txtOrdProductTotal = calculateProductTotal(product_qty,product_sprice);
                        $("#txtOrdProductTotal-" + idx + "-" + idxex).val(txtOrdProductTotal);
                        ajaxUpdateProductExchangeInformation(idx,idxex);
                    });
                }
                return success;
            }



            function calculateProductTotal(qty,salePrice){
                var total = 0;
                total = parseFloat(qty) * parseFloat(salePrice);
                return total;
            }
        </script>
        <script type="text/javascript">
            // -- Funciones para manejar la ventana modal de los UPCS
            function closePopUp()
            {
                $.modal.close();
            }


            function closeRefreshList(){
                $.modal.close();
                var ajax_url = "ajax/ajax_order_items_control_undelivered.php";
                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data: {
                        action: 'show_parts_list'
                    },
                    success: function(data){
                        //ajaxUpdateOrderTotals();
                        $("#div-order-products").html(data);
                    }
                });
            }


            function refreshOrderList(){

            }

            function refreshExchangeList(){
                var ajax_url = "ajax/ajax_exchange_items_control.php";
                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data: {
                        action: 'show_parts_list'
                    },
                    success: function(data){
                        $("#div-exchange-products").html(data);
                    }
                });
            }

            function loadPopupUpcs(idx,qty){
                var src = "includes/popup_upcs.php?idx=" + idx + "&qty=" + qty;
                function setSrc(){
                    $('#iframeid').attr("src", src);
                }

                $.modal('<iframe src="" id="iframeid" width="460" style="height:225px;border:0" frameBorder="0">', {
                    opacity:80,
                    overlayCss: {backgroundColor:"#fff"},
                    closeHTML:"",
                    containerCss:{
                        backgroundColor:"#fff",
                        borderColor:"#fff",
                        height:"225px",
                        width:"460px",
                        padding:0
                    },
                    overlayClose:true,
                    onShow: function(dialog) {
                        setSrc();
                    }
                });
            }


            function loadPopupWarehouses(idx){
                var src = "includes/popup_warehouses.php?idx=" + idx ;
                function setSrc(){
                    $('#iframeid').attr("src", src);
                }

                $.modal('<iframe src="" id="iframeid" width="560" style="height:225px;border:0" frameBorder="0">', {
                    opacity:80,
                    overlayCss: {backgroundColor:"#fff"},
                    closeHTML:"",
                    containerCss:{
                        backgroundColor:"#fff",
                        borderColor:"#fff",
                        height:"225px",
                        width:"560px",
                        padding:0
                    },
                    overlayClose:true,
                    onShow: function(dialog) {
                        setSrc();
                    }
                });
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#frmRegistroDevoluciones").submit(function() {
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),
                        // Mostramos un mensaje con la respuesta de PHP
                        success: function(data) {
                            if(data !=''){
                                var obJson = eval( "(" + data + ")" );
                                var exito = obJson ['exito'];
                                var msg = obJson ['msg'];

                                if(exito == '1'){
                                    var id_order = obJson ['id_order'];

                                    $.msgbox("Se guardaron los datos para la devolucion exitosamente.<br><br>- No. Orden: "+id_order, {
                                        type: "info",
                                        buttons : [
                                            {type: "submit", value: "Yes"}
                                        ]
                                    }, function(result) {
                                        location.href="retornos_cod.php?return=<?php echo $return_type; ?>&action=<?php echo $return_action; ?>";
                                    });
                                }else{
                                    $.msgbox("No se pudieron procesar los datos de la devolucion. <br>" + msg);
                                }
                            }else{
                                $.msgbox("Ocurrio un error al procesar los datos de la devolucion.", {
                                    type: "error" });
                            }
                        }
                    })
                    return false;
                });
            });

            function validarDatosFormulario(){
                var errores="";
                var errores_msg ="";

                with(document.frmRegistroDevoluciones){

                    if($("#txtCustomerID").val() =="" ){
                        errores += "- Debe seleccionar un Cliente.<br>";
                    }

                    if($("#txtPrevOrderID").val() =="" ){
                        errores += "- Debe introducir el numero de orden previo.<br>";
                    }

                    if($("#txtOrderNotes").val() =="" ){
                        errores += "- Debe introducir una nota para la orden.<br>";
                    }

                    if($("#txtShpDate").val() =="" ){
                        errores += "- Debe seleccionar la fecha de salida.<br>";
                    }

                    /*
                    if($("#txtPaymentDate").val() =="" ){
                        errores += "- Debe seleccionar una fecha para el pago.<br>";
                    }
                     */

                    if($("#lstProductPkgCondition").val() =="" ){
                        errores += "- Debe seleccionar la condici&oacute;n general del paquete.<br>";
                    }

                    if($("#lstItemCondition").val() =="" ){
                        errores += "- Debe seleccionar la condici&oacute;n de los productos.<br>";
                    }

                    if(SIZE_PRODUCT_LIST <= 0){
                        errores += "- Debe agregar por lo menos un item a la lista.<br>";
                    }  else if($("#hdHasWarehouse").val() ==""){
                        errores += "- Falta por seleccionar el almac&eacute;n de destino para algunos items.<br>";
                    }


                    if(return_type == "Exchange"){
                        if(SIZE_EXCHANGE_LIST <= 0){
                            errores += "- Debe existir por lo menos un item a la lista de productos para cambio fisico.<br>";
                        }
                    }
                }

                if(errores!='')
                {
                    errores_msg = "Verifique la siguiente informaci&oacute;n requerida:<br><br>"+errores;
                    $.msgbox(errores_msg, {
                        buttons : [
                            {type: "submit", value: "Aceptar"},
                        ]
                    });
                }
                else
                {
                    $.msgbox("Esta a punto de guardar la informacion del formulario, &iquest;Desea continuar?", {
                        type: "confirm",
                        buttons : [
                            {type: "submit", value: "Ok"},
                            {type: "cancel", value: "Cancelar"}
                        ]
                    }, function(result) {
                        if(result == "Ok"){
                            $("#subProcesar").click();
                        }
                    });
                }
            }
        </script>
        <script type="text/javascript">
            function validaFlotantes(value){
                var numero = 0.00;
                if(validar_flotante(value)){
                    numero = value;
                }
                return numero;
            }
        </script>
    </head>
    <body>
        <div id="contenedor">
            <?php include '../includes/header_bar.php'; ?>
            <div id="main-container" class="form-container" style="">
                <?php
                include_once $COMMON_PATH . '/common/php/popup/popup_vendors.php';
                ?>
                <form id="frmRegistroDevoluciones" name="frmRegistroDevoluciones" action="process/retorno_cod_procesar.php">
                    <input type='submit' id='subProcesar' style='display:none;'/>
                    <input type="hidden" name="hdReturnType" id="hdReturnType" style="visibility: hidden; display: none;" value="<?php echo $return_type ?>" />
                    <input type="hidden" name="hdReturnAction" id="hdReturnAction" style="visibility: hidden; display: none;" value="<?php echo $return_action ?>" />
                    <table width="100%">
                        <tr>
                            <td>
                                <fieldset class="field-set" style="min-width: 450px;">
                                    <legend>Datos del Proveedor</legend>
                                    <table>
                                        <tr>
                                            <td width="180px"><label>Proveedor ID:</label></td>
                                            <td  colspan="3">
                                                <span>
                                                    <input type="text" name="txtVendorID" id="txtVendorID" value="" class="text-box" size="10" readonly />
                                                    &nbsp;&nbsp;<img src="<?php echo $COMMON_PATH ?>/common/img/icsearchsmall.gif" border="0" style="cursor: pointer;" onclick="triggerPopupVendors()" />
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top"><label>Nombre: </label></td>
                                            <td  colspan="3">
                                                <span id="txtVendorName" class="label" style="width: 420px; display: block; font-weight: bold;  font-size: 8pt; "></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top"><label>Direcci&oacute;n:</label></td>
                                            <td  colspan="3">
                                                <span id="txtVendorAddress" class="label" style="width: 420px; display: block; font-weight: bold;  font-size: 8pt; "></span>
                                        </tr>                                        
                                    </table>
                                </fieldset>
                            </td>
                        <tr>
                        <tr>
                            <td>
                                <fieldset class="field-set" style="min-width: 450px;">
                                    <legend>Informaci&oacute;n de la Orden</legend>
                                    <table>
                                        <tr>
                                            <td width="180px"><label>No. Orden Original:</label></td>
                                            <td>
                                                <input type="text" name="txtPrevOrderID" id="txtPrevOrderID" value="" class="text-box" size="10"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Fecha: </label></td>
                                            <td>
                                                <input type="text" name="txtPODate" id="txtPODate"  class="text-box" size="10" readonly/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Terminos: </label></td>
                                            <td>
                                                <select name="lstPOTerms" id="lstPOTerms" class="text-box" >
                                                    <option value="">---</option>
                                                    <option value="Pre-payment">Pre-payment</option>
                                                    <option value="COD">COD</option>
                                                    <option value="Percentage Down/30 days Balance">Percentage Down/30 days Balance</option>
                                                    <option value="Other">Other</option>
                                                    <option value="Wire Transfer">Wire Transfer</option>
                                                    <option value="Consignment">Consignment</option>
                                                    <option value="2/10 Net 30">2/10 Net 30</option>
                                                    <option value="30 Days">30 Days</option>
                                                    <option value="60 Days">60 Days</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top"><label>Descripcion de Terminos: </label></td>
                                            <td><textarea name="txtPOTermsDesc" id="txtPOTermsDesc" class="text-box" cols="50" rows="2"></textarea></td>
                                        </tr>
                                        <tr>
                                            <td valign="top"><label>Mensajer&iacute;a de entrega: </label></td>
                                            <td><select name="lstPOShipVia" id="lstPOSihpVia" class="text-box" >
                                                    <option value="">---</option>
                                                    <option value="UPS">UPS</option>
                                                    <option value="USPS">USPS</option>
                                                    <option value="Fedex">Fedex</option>
                                                    <option value="Road Way">Road Way</option>
                                                    <option value="Other">Other</option>
                                                    <option value="DHL">DHL</option>
                                                </select>&nbsp;
                                                <input type="text" name="txtPOShipOther" id="txtPOShipOther" value="" class="text-box" size="25"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top"><label>Almacen de Entrega: </label></td>
                                            <td>
                                                <?php
                                                include 'includes/select_warehouses.php';
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Fecha ETD: </label></td>
                                            <td>
                                                <input type="text" name="txtPOEtd" id="txtPOEtd"  class="text-box" size="10" readonly/>
                                            </td>
                                        </tr>                                        
                                        <tr>
                                            <td><label>Notas: </label></td>
                                            <td><textarea name="txtOrderNotes" id="txtOrderNotes" class="text-box" cols="50" rows="2"></textarea></td>
                                        </tr>
                                        <!--
                                        <tr>
                                            <td><label>Fecha de Salida: </label></td>
                                            <td>
                                                <input type="text" name="txtShpDate" id="txtShpDate"  class="text-box" size="10" readonly/>
                                            </td>
                                        </tr>
                                        -->
                                    </table>
                                </fieldset>
                            </td>
                        </tr>

                        <tr style="display: none;">
                            <td>
                                <fieldset class="field-set" style="min-width: 450px;">
                                    <legend>Informaci&oacute;n del Pago</legend>
                                    <table>
                                        <tr>
                                            <td width="180px"><label>Fecha de Pago:</label></td>
                                            <td>
                                                <input type="text" name="txtPaymentDate" id="txtPaymentDate"  class="text-box" size="10" readonly/>&nbsp;&nbsp;
                                            </td>
                                        </tr>
                                        <!--
                                        <tr>
                                            <td valign="top"><label>Notes: </label></td>
                                            <td><textarea name="txtPaymentNotes" id="txtPaymentNotes" class="text-box" cols="50" rows="2"></textarea></td>
                                        </tr>
                                        -->
                                    </table>
                                </fieldset>
                            </td>
                        </tr>

                        <tr style="display: none;">
                            <td>
                                <fieldset class="field-set">
                                    <legend>Datos para el Retorno de la Mercancia</legend>
                                    <table>
                                        <tr>
                                            <td><label>Condici&oacute;n general del paquete: </label></td>
                                            <td>
                                                <select name="lstProductPkgCondition" id="lstProductPkgCondition" class="text-box" >
                                                    <option value="">--- Seleccione ---</option>
                                                    <option value="New/Undeliverable" >Nuevo / No se puede entregar</option>
                                                    <option value="Damage/Undeliverable" >Da&ntilde;ado / No se puede entregar</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Condici&oacute;n de los productos</label></td>
                                            <td>
                                                <select name="lstItemCondition" id="lstItemCondition" class="text-box">
                                                    <option value="">--- Seleccione ---</option>
                                                    <option value="New">New</option>
                                                    <option value="Good Missing Parts">Good Missing Parts</option>
                                                    <option value="Fair Missing Part" >Fair Missing Part</option>
                                                    <option value="Complete Good">Complete Good</option>
                                                    <option value="Complete Fair">Complete Fair</option>
                                                    <option value="Damage">Damage</option>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td valign="top" width="180px"><label>Notas: </label></td>
                                            <td><textarea name="txtGeneralReturnNotes" id="txtGeneralReturnNotes" class="text-box" cols="50" rows="2"></textarea></td>
                                        </tr>
                                        <tr>
                                            <td><label>Chofer: </label></td>
                                            <td>
                                                <?php include_once 'includes/select_virtual_warehouses.php'; ?>
                                            </td>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>

                        <tr style="display: none;">
                            <td>
                                <fieldset class="field-set">
                                    <legend></legend>
                                    <table>
                                        <tr>
                                            <td><label>Diferencia de $: </label></td>
                                            <td>
                                                <label>
                                                    $&nbsp;<input type="text" name="txtAmountDiff" id="txtAmountDiff"  class="text-box" size="10" onchange="this.value=validaFlotantes(this.value)" style="text-align: right" value="0" />
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </form>
                <br>
                <center><input type="button" name="" value="Agregar Producto"  class="button" onclick="triggerPopupParts('popup-parts1',60,'')"/></center>
                <br>
                <table width="99%" align="center"  class="List">
                    <tr class="tableListTittle">
                        <td colspan="6" align="center">LISTA DE PRODUCTOS</td>
                    </tr>
                    <tr>
                        <td>
                            <div id="div-order-products" style="width: 100%"></div>
                        </td>
                    </tr>
                </table>

                <br>
                <div id ="div-order-totals" style="display: none;">
                    <table width="99%" align="center" class="List">
                        <tr class="tableListColumn">
                            <td colspan="5" align="center">Total de la Orden</td>
                        </tr>
                        <tr class="tableFila0">
                            <td align="center">Total de Items</td>
                            <td align="center">Total de Envio</td>
                            <td align="center">Total de Descuento</td>
                            <td align="center">Impuestos</td>
                            <td align="center">Importe Neto</td>
                        </tr>
                        <tr class="tableFila1">
                            <td align="center"><span id="spnOrderItemsTotal"></span></td>
                            <td align="center"><span id="spnOrderItemsTotalShip"></span><!-- <input class="text-box" type="text" name="txtOrderShip" id="txtOrderShip"  value="0.00" style="text-align: right;" onkeyup="this.value = validar_numerico_keyup(this.value)" onfocus="$(this).select()" onclick="$(this).select()" size="7" /> --></td>
                            <td align="center"><span id="spnOrderItemsTotalDisc"></span><!-- <input class="text-box" type="text" name="txtOrderDisc" id="txtOrderDisc"  value="0.00" style="text-align: right;" onkeyup="this.value = validar_numerico_keyup(this.value)" onfocus="$(this).select()" onclick="$(this).select()" size="7" /> --></td>
                            <td align="center"><span id="spnOrderItemsTotalTax"></span><!-- <input class="text-box" type="text" name="txtOrderTax" id="txtOrderTax"  value="0.00" style="text-align: right;" onkeyup="this.value = validar_numerico_keyup(this.value)" onfocus="$(this).select()" onclick="$(this).select()" size="7" /> --></td>
                            <td align="center">$&nbsp;<span id="spnOrderTotal"></span></td>
                        </tr>
                    </table>
                </div>
                <br>
                <?php
                if ($return_type == 'Exchange') {
                    include 'includes/div_exchange_products.php';
                }
                ?>
                <div style="text-align: center;">
                    <input type="button" name="" value="Guardar Informaci&oacute;n"  class="button" onclick="validarDatosFormulario()"/>
                </div>

                <!--
                <input type="button" onclick="generatePaginator('paginador','ajax/www.com',25);" />
                <div id=paginador style="left: 42%;width: 350px"></div>
                -->
            </div>
        </div>
    </body>
</html>