<?php

canView('TeamManager', $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], $id);