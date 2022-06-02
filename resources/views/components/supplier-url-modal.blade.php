@push('styles')
    <link rel="stylesheet" href="{{ asset('static/css/supplier-url-modal.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('static/js/supplier-url-modal.js') }}"></script>
@endpush


<div id="purchase_link_modal">
    <form id="purchase_link_form" onsubmit="saveUrlModal(event)">
        <div id="purchase_link_form_close">
            <i class="fa-solid fa-xmark" onclick="urlModalClose()"></i>
        </div>
        <fieldset id="purchase_link_form_content">
            <strong>Link para compra</strong>
            <div id="purchase_link_form_id" style="visibility: hidden;"></div>
            <div id="purchase_link_form_online_order_number">
                <strong>ORIGEM:</strong><span></span>
            </div>
            <div>
                <label for="supplier_url">URL</label>
                <input type="text" name="supplier_url" id="purchase_link_formsupplier_url">
            </div>
            <div id="purchase_link_form_submit"><button type="submit">Salvar</button></div>
        </fieldset>
    </form>
</div>
