<div class="modal fade" id="fullSpecsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"> <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom p-3">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-cpu-fill me-2 text-primary"></i>Thông số kỹ thuật chi tiết
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0"> <table class="table table-hover align-middle mb-0">
                    <tbody>
                        <?php if (!empty($specs)): ?>
                            <?php foreach ($specs as $spec): ?>
                                <tr>
                                    <td class="text-muted py-3 ps-4" style="width: 40%; font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($spec['spec_key']); ?>
                                    </td>
                                    <td class="fw-bold py-3 text-dark pe-4" style="font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($spec['spec_value']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">Không có dữ liệu thông số.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer border-top p-3">
                <button type="button" class="btn btn-secondary w-100 fw-bold py-2" data-bs-dismiss="modal">
                    ĐÓNG
                </button>
            </div>
        </div>
    </div>
</div>