<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold" id="profileModalLabel">
                    <i class="fas fa-id-card me-2 text-primary"></i>Thông tin cá nhân
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center p-4">
                <div class="position-relative d-inline-block mb-3">
                    <img src="admin_images/<?php echo $admin_image; ?>" alt="Admin Avatar"
                        class="rounded-circle border border-4 border-white shadow"
                        style="width: 130px; height: 130px; object-fit: cover;">
                    <span
                        class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                        style="width: 18px; height: 18px;" title="Đang hoạt động"></span>
                </div>

                <h4 class="fw-bold mb-1"><?php echo $admin_name; ?></h4>
                <div class="mb-4">
                    <span class="badge rounded-pill bg-primary px-3 py-2" style="font-weight: 500; font-size: 0.8rem;">
                        <i class="fas fa-shield-alt me-1"></i> <?php echo strtoupper($admin_role); ?>
                    </span>
                </div>

                <hr class="text-muted opacity-25">

                <div class="text-start mt-4 px-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box me-3 bg-light rounded text-center"
                            style="width: 35px; height: 35px; line-height: 35px;">
                            <i class="fas fa-envelope text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Email</small>
                            <span class="fw-medium"><?php echo $admin_email ?? 'Chưa cập nhật' ?></span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box me-3 bg-light rounded text-center"
                            style="width: 35px; height: 35px; line-height: 35px;">
                            <i class="fas fa-phone text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Số điện thoại</small>
                            <span class="fw-medium"><?php echo $admin_phone ?? 'Chưa cập nhật'; ?></span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="icon-box me-3 bg-light rounded text-center"
                            style="width: 35px; height: 35px; line-height: 35px;">
                            <i class="fas fa-calendar-check text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Ngày tham gia</small>
                            <span class="fw-medium"><?php echo date('d/m/Y', strtotime($admin_join)); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light justify-content-center p-3">
                <button type="button" class="btn btn-outline-secondary btn-sm"
                    style="min-width: 450px; border-radius: 5px;" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>