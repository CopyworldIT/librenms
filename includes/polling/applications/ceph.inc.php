<?php

use LibreNMS\RRD\RrdDefinition;

$name = 'ceph';
if (!empty($agent_data['app'][$name])) {
    $ceph_data = $agent_data['app'][$name];
    $app_id = $app['app_id'];

    $metrics = array();
    foreach (explode('<', $ceph_data) as $section) {
        if (empty($section)) {
            continue;
        }
        list($section, $data) = explode('>', $section);

        if ($section == 'cephstatus') {

        } else if ($section == 'pgstates') {
            $rrd_def = RrdDefinition::make()
                ->addDataset('creating', 'GAUGE', 0)
                ->addDataset('activating', 'GAUGE', 0)
                ->addDataset('active', 'GAUGE', 0)
                ->addDataset('clean', 'GAUGE', 0)
                ->addDataset('down', 'GAUGE', 0)
                ->addDataset('scrubbing', 'GAUGE', 0)
                ->addDataset('deep', 'GAUGE', 0)
                ->addDataset('degraded', 'GAUGE', 0)
                ->addDataset('inconsistent', 'GAUGE', 0)
                ->addDataset('peering', 'GAUGE', 0)
                ->addDataset('repair', 'GAUGE', 0)
                ->addDataset('recovering', 'GAUGE', 0)
                ->addDataset('forced_recovery', 'GAUGE', 0)
                ->addDataset('recovery_wait', 'GAUGE', 0)
                ->addDataset('recovery_toofull', 'GAUGE', 0)
                ->addDataset('recovery_unfound', 'GAUGE', 0)
                ->addDataset('backfilling', 'GAUGE', 0)
                ->addDataset('forced_backfill', 'GAUGE', 0)
                ->addDataset('backfill_wait', 'GAUGE', 0)
                ->addDataset('backfill_toofull', 'GAUGE', 0)
                ->addDataset('backfill_unfound', 'GAUGE', 0)
                ->addDataset('incomplete', 'GAUGE', 0)
                ->addDataset('stale', 'GAUGE', 0)
                ->addDataset('remapped', 'GAUGE', 0)
                ->addDataset('undersized', 'GAUGE', 0)
                ->addDataset('peered', 'GAUGE', 0)
                ->addDataset('snaptrim', 'GAUGE', 0)
                ->addDataset('snaptrim_wait', 'GAUGE', 0)
                ->addDataset('snaptrim_error', 'GAUGE', 0);

            $fields = [
                'creating' => '0',
                'activating' => '0',
                'active' => '0',
                'clean' => '0',
                'down' => '0',
                'scrubbing' => '0',
                'deep' => '0',
                'degraded' => '0',
                'inconsistent' => '0',
                'peering' => '0',
                'repair' => '0',
                'recovering' => '0',
                'forced_recovery' => '0',
                'recovery_wait' => '0',
                'recovery_toofull' => '0',
                'recovery_unfound' => '0',
                'backfilling' => '0',
                'forced_backfill' => '0',
                'backfill_wait' => '0',
                'backfill_toofull' => '0',
                'backfill_unfound' => '0',
                'incomplete' => '0',
                'stale' => '0',
                'remapped' => '0',
                'undersized' => '0',
                'peered' => '0',
                'snaptrim' => '0',
                'snaptrim_wait' => '0',
                'snaptrim_error' => '0',
            ];

            $rrd_name = ['app', $name, $app_id, 'pgstates'];

            foreach (explode("\n", $data) as $line) {
                if (empty($line)) continue;
                list($state_name, $count) = explode(':', $line);
                $fields[$state_name] = $count;
            }

            print "Ceph Placement Group States:\n";
            d_echo($fields);

            $metrics["pgstates"] = $fields;
            $tags = compact('name', 'app_id', 'pgstates', 'rrd_name', 'rrd_def');
            data_update($device, 'app', $tags, $fields);

        } else if ($section == "poolstats") {
            $rrd_def = RrdDefinition::make()
                ->addDataset('ops', 'GAUGE', 0)
                ->addDataset('wrbytes', 'GAUGE', 0)
                ->addDataset('rbytes', 'GAUGE', 0);

            foreach (explode("\n", $data) as $line) {
                if (empty($line)) {
                    continue;
                }
                list($pool,$ops,$wrbytes,$rbytes) = explode(':', $line);
                $rrd_name = array('app', $name, $app_id, 'pool', $pool);

                print "Ceph Pool: $pool, IOPS: $ops, Wr bytes: $wrbytes, R bytes: $rbytes\n";
                $fields = array(
                    'ops' => $ops,
                    'wrbytes' => $wrbytes,
                    'rbytes' => $rbytes
                );
                $metrics["pool_$pool"] = $fields;
                $tags = compact('name', 'app_id', 'pool', 'rrd_name', 'rrd_def');
                data_update($device, 'app', $tags, $fields);
            }
        } elseif ($section == "osdperformance") {
            $rrd_def = RrdDefinition::make()
                ->addDataset('apply_ms', 'GAUGE', 0)
                ->addDataset('commit_ms', 'GAUGE', 0);

            foreach (explode("\n", $data) as $line) {
                if (empty($line)) {
                    continue;
                }
                list($osd,$apply,$commit) = explode(':', $line);
                $rrd_name = array('app', $name, $app_id, 'osd', $osd);

                print "Ceph OSD: $osd, Apply: $apply, Commit: $commit\n";
                $fields = array(
                    'apply_ms' => $apply,
                    'commit_ms' => $commit
                );
                $metrics["osd_$osd"] = $fields;
                $tags = compact('name', 'app_id', 'osd', 'rrd_name', 'rrd_def');
                data_update($device, 'app', $tags, $fields);
            }
        } elseif ($section == "df") {
            $rrd_def = RrdDefinition::make()
                ->addDataset('avail', 'GAUGE', 0)
                ->addDataset('used', 'GAUGE', 0)
                ->addDataset('objects', 'GAUGE', 0);

            foreach (explode("\n", $data) as $line) {
                if (empty($line)) {
                    continue;
                }
                list($df,$avail,$used,$objects) = explode(':', $line);
                $rrd_name = array('app', $name, $app_id, 'df', $df);

                print "Ceph Pool DF: $df, Avail: $avail, Used: $used, Objects: $objects\n";
                $fields = array(
                    'avail' => $avail,
                    'used' => $used,
                    'objects' => $objects
                );
                $metrics["df_$df"] = $fields;
                $tags = compact('name', 'app_id', 'df', 'rrd_name', 'rrd_def');
                data_update($device, 'app', $tags, $fields);
            }
        }
    }
    update_application($app, $ceph_data, $metrics);
}

unset($ceph_data, $metrics);
