<?php
if (!defined('ABSPATH')) exit;


// Shortcode: Room Reservation Form
add_shortcode('room_reservation_form', function () {
    ob_start(); ?>
    
    <!-- Room Reservation Modal -->
    <div id="rrs-modal" class="modal-hidden">
        <div class="rrs-modal-content">
            <h2 class="rrs-header my-3"><?php echo esc_html__('Room Reservation', 'room-reservation-system'); ?></h2>

            <form id="rrs-reservation-form">
                <!-- User Details -->
                <input class="rrs-input my-1" type="text" name="name" placeholder="Full Name" required>
                <input class="rrs-input my-1" type="text" name="college" placeholder="College" required>
                <input class="rrs-input my-1" type="text" name="course" placeholder="Course" required>
                <input class="rrs-input my-1" type="email" name="email" placeholder="UP Email" required>

                <!-- Room & Time Selectors -->
                <div class="select-container d-flex flex-row justify-content-between align-items-center gap-2 w-100">

                    <!-- Room Dropdown -->
                    <select class="my-1 rrs-dropdown" name="room" required>
                        <option value="" disabled selected hidden><?php echo esc_html__('Select Room', 'room-reservation-system'); ?></option>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?php echo esc_attr("Room $i"); ?>">
                                <?php echo esc_html("Room $i"); ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <!-- Time Dropdown -->
                    <select class="my-1 rrs-dropdown" name="time" id="rrs-time-dropdown" required>
                        <option value="" disabled selected hidden><?php echo esc_html__('Select Time', 'room-reservation-system'); ?></option>
                        <?php for ($i = 8; $i <= 16; $i++): ?>
                            <?php
                                $time_value      = sprintf('%02d:00', $i);
                                $time_label_start = date('g A', strtotime($time_value));
                                $time_label_end   = date('g A', strtotime(($i + 1) . ":00"));
                                $label = $time_label_start . ' - ' . $time_label_end;
                            ?>
                            <option value="<?php echo esc_attr($time_value); ?>">
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                </div>

                <!-- Date Picker -->
                <input class="rrs-input my-1" type="text" name="date" id="rrs-date-picker" placeholder="Select a date" required>

                <!-- Buttons -->
                <div class="rrs-button-container d-flex justify-content-between align-items-center gap-2 flex-row my-3">
                    <button id="rrs-close-modal" type="button"><?php echo esc_html__('Cancel', 'room-reservation-system'); ?></button>
                    <button class="rrs-submit-modal" type="submit"><?php echo esc_html__('Submit', 'room-reservation-system'); ?></button>
                </div>

                <!-- AJAX Response -->
                <div id="rrs-response"></div>
            </form>
        </div>
    </div>

    <!-- Success Popup -->
    <div id="rrs-success-popup" class="modal-hidden">
        <div class="rrs-success-content">
            <p id="rrs-success-message"></p>
            <button id="rrs-ok-button"><?php echo esc_html__('OK', 'room-reservation-system'); ?></button>
        </div>
    </div>

    <?php return ob_get_clean();
});

// Shortcode: Room Reservation Calendar
add_shortcode('room_reservation_calendar', function () {
    ob_start(); ?>
    <div class="d-flex flex-row align-items-center gap-3 mb-3">
        <select id="rrs-room-select">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="<?php echo esc_attr("Room $i"); ?>"><?php echo esc_html("Room $i"); ?></option>
            <?php endfor; ?>
        </select>
        <button id="rrs-open-modal"><?php echo esc_html__('Reserve a Room', 'room-reservation-system'); ?></button>
    </div>
    <div id="rrs-calendar"></div>
    <?php return ob_get_clean();
});
