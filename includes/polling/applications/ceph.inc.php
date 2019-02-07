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
            $rrd_def = RrdDefinition::make()
                ->addDataset('health', 'GAUGE', 0)
                ->addDataset('num_osds', 'GAUGE', 0)
                ->addDataset('num_up_osds', 'GAUGE', 0)
                ->addDataset('num_down_osds', 'GAUGE', 0)
                ->addDataset('num_in_osds', 'GAUGE', 0)
                ->addDataset('num_out_osds', 'GAUGE', 0)
                ->addDataset('num_remapped_pgs', 'GAUGE', 0)
                ->addDataset('nearfull', 'GAUGE', 0)
                ->addDataset('full', 'GAUGE', 0)
                ->addDataset('num_pgs', 'GAUGE', 0)
                ->addDataset('num_pools', 'GAUGE', 0)
                ->addDataset('num_objects', 'GAUGE', 0)
                ->addDataset('data_bytes', 'GAUGE', 0)
                ->addDataset('bytes_used', 'GAUGE', 0)
                ->addDataset('bytes_avail', 'GAUGE', 0)
                ->addDataset('bytes_total', 'GAUGE', 0)
                ->addDataset('read_bytes_sec', 'GAUGE', 0)
                ->addDataset('write_bytes_sec', 'GAUGE', 0)
                ->addDataset('read_op_per_sec', 'GAUGE', 0)
                ->addDataset('write_op_per_sec', 'GAUGE', 0);

            $fields = [
                'health' => 0,
                'num_osds' => 0,
                'num_up_osds' => 0,
                'num_down_osds' => 0,
                'num_in_osds' => 0,
                'num_out_osds' => 0,
                'num_remapped_pgs' => 0,
                'nearfull' => 0,
                'full' => 0,
                'num_pgs' => 0,
                'num_pools' => 0,
                'num_objects' => 0,
                'data_bytes' => 0,
                'bytes_used' => 0,
                'bytes_avail' => 0,
                'bytes_total' => 0,
                'read_bytes_sec' => 0,
                'write_bytes_sec' => 0,
                'read_op_per_sec' => 0,
                'write_op_per_sec' => 0
            ];

            // Map ceph health status to integer so it can be stored in rrd and influx.
            $health_map = [
                'HEALTH_OK' => 0,
                'HEALTH_WARN' => 1,
                'HEALTH_CRIT' => 2,
                'HEALTH_ERR' => 3,
                'UNKNOWN' => 4
            ];

            $rrd_name = ['app', $name, $app_id, 'cephstatus'];

            foreach (explode("\n", $data) as $line) {
                if (empty($line)) continue;
                list($field_name, $value) = explode(':', $line);
                if ($field_name == 'health') {
                    $fields['health'] = $health_map[$value];
                } else {
                    $fields[$field_name] = intval($value);
                }
            }

            print "Ceph Status:\n";
            d_echo($fields);

            $metrics["cephstatus"] = $fields;
            $tags = compact('name', 'app_id', 'cephstatus', 'rrd_name', 'rrd_def');
            data_update($device, 'app', $tags, $fields);

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
                'creating' => 0,
                'activating' => 0,
                'active' => 0,
                'clean' => 0,
                'down' => 0,
                'scrubbing' => 0,
                'deep' => 0,
                'degraded' => 0,
                'inconsistent' => 0,
                'peering' => 0,
                'repair' => 0,
                'recovering' => 0,
                'forced_recovery' => 0,
                'recovery_wait' => 0,
                'recovery_toofull' => 0,
                'recovery_unfound' => 0,
                'backfilling' => 0,
                'forced_backfill' => 0,
                'backfill_wait' => 0,
                'backfill_toofull' => 0,
                'backfill_unfound' => 0,
                'incomplete' => 0,
                'stale' => 0,
                'remapped' => 0,
                'undersized' => 0,
                'peered' => 0,
                'snaptrim' => 0,
                'snaptrim_wait' => 0,
                'snaptrim_error' => 0,
            ];

            $rrd_name = ['app', $name, $app_id, 'pgstates'];

            foreach (explode("\n", $data) as $line) {
                if (empty($line)) continue;
                list($state_name, $count) = explode(':', $line);
                $fields[$state_name] = intval($count);
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
                ->addDataset('rbytes', 'GAUGE', 0)
                ->addDataset('read_ops', 'GAUGE', 0)
                ->addDataset('write_ops', 'GAUGE', 0);

            foreach (explode("\n", $data) as $line) {
                if (empty($line)) {
                    continue;
                }
                list($pool, $ops, $wrbytes, $rbytes, $read_ops, $write_ops) = explode(':', $line);
                $rrd_name = array('app', $name, $app_id, 'pool', $pool);

                print "Ceph Pool: $pool, Total IOPS: $ops, Read IOPS: $read_ops, Write IOPS: $write_ops, Wr bytes: $wrbytes, R bytes: $rbytes\n";
                $fields = array(
                    'ops' => $ops,
                    'wrbytes' => $wrbytes,
                    'rbytes' => $rbytes,
                    'read_ops' => $read_ops,
                    'write_ops' => $write_ops
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
