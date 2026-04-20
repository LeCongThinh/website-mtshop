<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title text-primary fw-bold mb-0">ĐƠN HÀNG: #<span id="od-code"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center">
                            <h6 class="text-uppercase fw-bold m-0" style="font-size: 0.85rem; color: #4b5563;">Thông tin
                                nhận hàng</h6>
                        </div>
                        <div class="bg-light rounded-4 p-3 border">
                            <div class="d-flex mb-2">
                                <div class="text-muted small" style="width: 150px;">Người nhận:</div>
                                <div class="fw-bold small text-dark" id="od-receiver-name">---</div>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="text-muted small" style="width: 150px;">Số điện thoại:</div>
                                <div class="fw-semibold small text-dark" id="od-receiver-phone">---</div>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="text-muted small" style="width: 150px;">Địa chỉ:</div>
                                <div class="flex-grow-1 text-muted small" id="od-receiver-address">---</div>
                            </div>
                            <div id="od-note-container"
                                class="mt-3 p-2 bg-white rounded border-start border-3 border-warning"
                                style="display:none">
                                <div class="text-muted small fst-italic" style="width: 150px;">Ghi chú:</div>
                                <div class="text-dark small fst-italic" id="od-note"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="d-flex align-items-center">
                            <h6 class="text-uppercase fw-bold m-0" style="font-size: 0.85rem; color: #4b5563;">Giao dịch
                            </h6>
                        </div>
                        <div class="bg-light rounded-4 p-3 border h-100">
                            <div class="mb-3">
                                <label class="d-block small text-muted mb-1">Thanh toán qua:</label>
                                <span class="bg-success-subtle text-success border border-success rounded-3 px-2 py-1 fw-bold"
                                    id="od-payment-method">---</span>
                            </div>
                            <div class="mb-3">
                                <label class="d-block small text-muted mb-1">Trạng thái thanh toán:</label>
                                <div id="od-payment-status">---</div>
                            </div>
                            <div class="border-top pt-2">
                                <span class="small text-muted">Ngày đặt: </span>
                                <span class="small fw-bold text-dark" id="od-date">---</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive border rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-uppercase">
                                <th>Sản phẩm</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="od-items-list" class="small">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">TỔNG CỘNG:</td>
                                <td class="text-end text-danger fw-bold fs-5" id="od-total-amount">0đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>