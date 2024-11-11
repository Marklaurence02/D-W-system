<?php if ($result->num_rows > 0): ?>
    <table class='table'>
        <thead>
            <tr>
                <th class='text-center'>S.N.</th>
                <th class='text-center'>Action By (Role)</th>
                <th class='text-center'>Action Type</th>
                <th class='text-center'>Details</th>
                <th class='text-center'>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = $offset + 1;
            while ($row = $result->fetch_assoc()):
                $userNameWithRole = htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . " (" . htmlspecialchars($row['role']) . ")";
            ?>
                <tr>
                    <td class='text-center'><?= $count ?></td>
                    <td class='text-center'><?= $userNameWithRole ?></td>
                    <td class='text-center'><?= htmlspecialchars($row['action_type']) ?></td>
                    <td class='text-center'><?= htmlspecialchars($row['action_details']) ?></td>
                    <td class='text-center'><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php $count++; endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class='pagination'>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href='#' class='page-link <?= $i == $page ? "active-page" : "" ?>' data-page='<?= $i ?>'><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php else: ?>
    <p>No activity logs found.</p>
<?php endif; ?>
