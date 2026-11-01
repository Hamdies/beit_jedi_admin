"use strict";

// Replacement for google.maps.drawing.DrawingManager, which Google removed in May 2026.
// Click the map to add points, then click the first point (or double click) to close the shape.
// Fires "overlaycomplete" with {type, overlay} like DrawingManager, and "overlaychange" when
// the finished polygon's points are dragged.
class PolygonDrawer {
    constructor(map, polygonOptions = {}) {
        this.map = map;
        this.polygonOptions = polygonOptions;
        this.points = [];
        this.drawing = true;

        this.line = new google.maps.Polyline({ map, clickable: false, strokeColor: "#1a73e8", strokeWeight: 2 });
        this.guide = new google.maps.Polyline({ map, clickable: false, strokeColor: "#1a73e8", strokeOpacity: 0.5, strokeWeight: 2 });

        this.addControls();
        this.setDrawing(true);

        map.addListener("click", (e) => this.addPoint(e.latLng));
        map.addListener("dblclick", () => this.finish());
        map.addListener("mousemove", (e) => {
            if (this.drawing && this.points.length) {
                this.guide.setPath([this.points[this.points.length - 1], e.latLng, this.points[0]]);
            }
        });
    }

    addControls() {
        const wrap = document.createElement("div");
        wrap.style.cssText = "display:flex;margin-top:8px;background:#fff;border-radius:3px;box-shadow:0 2px 6px rgba(0,0,0,.3)";
        const button = (title, svg, drawing) => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.title = title;
            btn.innerHTML = svg;
            btn.style.cssText = "border:0;background:none;padding:6px;cursor:pointer;display:flex";
            btn.addEventListener("click", () => this.setDrawing(drawing));
            wrap.appendChild(btn);
            return btn;
        };
        this.handBtn = button("Hand tool: move the map",
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2"><path d="M18 11V6a2 2 0 0 0-4 0v5M14 10V4a2 2 0 0 0-4 0v6M10 10.5V6a2 2 0 0 0-4 0v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg>', false);
        this.drawBtn = button("Shape tool: click points, then click the first point to finish",
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2"><polygon points="12 3 21 10 17 21 7 21 3 10"/></svg>', true);
        this.map.controls[google.maps.ControlPosition.TOP_CENTER].push(wrap);
    }

    setDrawing(drawing) {
        this.drawing = drawing;
        this.map.setOptions({ draggableCursor: drawing ? "crosshair" : null, disableDoubleClickZoom: drawing, clickableIcons: !drawing });
        this.handBtn.style.background = drawing ? "none" : "#e8f0fe";
        this.drawBtn.style.background = drawing ? "#e8f0fe" : "none";
        if (!drawing) this.clear();
    }

    addPoint(latLng) {
        if (!this.drawing) return;
        if (this.points.length >= 3 && this.isNear(this.points[0], latLng)) {
            this.finish();
            return;
        }
        // The two clicks of a double click land on the same spot.
        if (this.points.length && this.isNear(this.points[this.points.length - 1], latLng)) return;
        this.points.push(latLng);
        this.line.setPath(this.points);
    }

    isNear(first, second) {
        const projection = this.map.getProjection();
        if (!projection) return false;
        const scale = Math.pow(2, this.map.getZoom());
        const a = projection.fromLatLngToPoint(first);
        const b = projection.fromLatLngToPoint(second);
        return Math.hypot((a.x - b.x) * scale, (a.y - b.y) * scale) < 12;
    }

    finish() {
        if (!this.drawing || this.points.length < 3) return;
        const overlay = new google.maps.Polygon({ ...this.polygonOptions, paths: this.points, map: this.map });
        this.clear();
        const changed = () => google.maps.event.trigger(this, "overlaychange", { type: "polygon", overlay });
        ["set_at", "insert_at", "remove_at"].forEach((name) => overlay.getPath().addListener(name, changed));
        google.maps.event.trigger(this, "overlaycomplete", { type: "polygon", overlay });
    }

    clear() {
        this.points = [];
        this.line.setPath([]);
        this.guide.setPath([]);
    }
}
