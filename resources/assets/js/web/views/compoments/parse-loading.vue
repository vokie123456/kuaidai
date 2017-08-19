<template>
    <div id="parse-loading" v-show="load">
        <div class="modal"></div>

        <div class="tagscloud">
            <a
                v-for="(label, index) in labels"
                :key="index"
                :class="{tagc1: index % 3 === 0, tagc2: index % 3 === 1, tagc5: index % 3 === 2}"
            >
                {{label}}
            </a>


            <!--<a class="tagc2">姓名</a>-->
            <!--<a class="tagc5">身份证号</a>-->
            <!--<a class="tagc2">借款</a>-->
            <!--<a class="tagc2">姓名</a>-->
            <!--<a class="tagc2">借款</a>-->
            <!--<a class="tagc5">身份证号</a>-->
            <!--<a class="tagc2">姓名</a>-->
            <!--<a class="tagc5">借款</a>-->
            <!--<a class="tagc2">身份证号表</a>-->
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                mcList: [],
                radius: 90,
                dtr: Math.PI / 180,
                lasta: 1,
                lastb: 1,
                distr: true,
                tspeed: 11,
                size: 200,
                mouseX: 0,
                mouseY: 10,
                howElliptical: 1,
                aA: null,
                oDiv: null,
                d: 200,

                sa: null,
                ca: null,
                sb: null,
                cb: null,
                sc: null,
                cc: null,

                loadTimer: null,
            };
        },
        props: ['load', 'labels'],
        methods: {
            update() {
                let a, b, c = 0;

                a = (Math.min(Math.max(-this.mouseY, -this.size), this.size) / this.radius) * this.tspeed;
                b = (-Math.min(Math.max(-this.mouseX, -this.size), this.size) / this.radius) * this.tspeed;
                this.lasta = a;
                this.lastb = b;
                if (Math.abs(a) <= 0.01 && Math.abs(b) <= 0.01) {
                    return;
                }

                this.sineCosine(a, b, c);
                for (let i = 0; i < this.mcList.length; i++) {
                    if (this.mcList[i].on) {
                        continue;
                    }
                    let rx1 = this.mcList[i].cx;
                    let ry1 = this.mcList[i].cy * this.ca + this.mcList[i].cz * (-this.sa);
                    let rz1 = this.mcList[i].cy * this.sa + this.mcList[i].cz * this.ca;

                    let rx2 = rx1 * this.cb + rz1 * this.sb;
                    let ry2 = ry1;
                    let rz2 = rx1 * (-this.sb) + rz1 * this.cb;

                    let rx3 = rx2 * this.cc + ry2 * (-this.sc);
                    let ry3 = rx2 * this.sc + ry2 * this.cc;
                    let rz3 = rz2;

                    this.mcList[i].cx = rx3;
                    this.mcList[i].cy = ry3;
                    this.mcList[i].cz = rz3;

                    let per = this.d / (this.d + rz3);

                    this.mcList[i].x = (this.howElliptical * rx3 * per) - (this.howElliptical * 2);
                    this.mcList[i].y = ry3 * per;
                    this.mcList[i].scale = per;
                    let alpha = per;
                    alpha = (alpha - 0.6) * (10 / 6);
                    this.mcList[i].alpha = alpha * alpha * alpha - 0.2;
                    this.mcList[i].zIndex = Math.ceil(100 - Math.floor(this.mcList[i].cz));
                }
                this.doPosition();
            },
            positionAll() {
                let phi = 0;
                let theta = 0;
                let max = this.mcList.length;
                for (let i = 0; i < max; i++) {
                    if (this.distr) {
                        phi = Math.acos(-1 + (2 * (i + 1) - 1) / max);
                        theta = Math.sqrt(max * Math.PI) * phi;
                    } else {
                        phi = Math.random() * (Math.PI);
                        theta = Math.random() * (2 * Math.PI);
                    }
                    //坐标变换
                    this.mcList[i].cx = this.radius * Math.cos(theta) * Math.sin(phi);
                    this.mcList[i].cy = this.radius * Math.sin(theta) * Math.sin(phi);
                    this.mcList[i].cz = this.radius * Math.cos(phi);

                    this.aA[i].style.left = this.mcList[i].cx + this.oDiv.offsetWidth / 2 - this.mcList[i].offsetWidth / 2 + 'px';
                    this.aA[i].style.top = this.mcList[i].cy + this.oDiv.offsetHeight / 2 - this.mcList[i].offsetHeight / 2 + 'px';
                }
            },
            doPosition() {
                let l = this.oDiv.offsetWidth / 2;
                let t = this.oDiv.offsetHeight / 2;
                for (let i = 0; i < this.mcList.length; i++) {
                    if (this.mcList[i].on) {
                        continue;
                    }
                    let aAs = this.aA[i].style;
                    if (this.mcList[i].alpha > 0.1) {
                        if (aAs.display !== '')
                            aAs.display = '';
                    } else {
                        if (aAs.display !== 'none')
                            aAs.display = 'none';
                        continue;
                    }
                    aAs.left = this.mcList[i].cx + l - this.mcList[i].offsetWidth / 2 + 'px';
                    aAs.top = this.mcList[i].cy + t - this.mcList[i].offsetHeight / 2 + 'px';
                    aAs.filter = "alpha(opacity=" + 100 * this.mcList[i].alpha + ")";
                    aAs.zIndex = this.mcList[i].zIndex;
                    aAs.opacity = this.mcList[i].alpha;
                }
            },
            sineCosine(a, b, c) {
                this.sa = Math.sin(a * this.dtr);
                this.ca = Math.cos(a * this.dtr);
                this.sb = Math.sin(b * this.dtr);
                this.cb = Math.cos(b * this.dtr);
                this.sc = Math.sin(c * this.dtr);
                this.cc = Math.cos(c * this.dtr);
            }
        },
        watch: {
            load(val) {
                let self = this;

                if (!val && this.loadTimer) {
                    clearInterval(this.loadTimer);
                    this.loadTimer = null;
                } else if (val && this.loadTimer === null) {
                    this.oDiv = document.querySelector('.tagscloud');
                    this.aA = this.oDiv.getElementsByTagName('a');
                    let i = 0;
                    this.mcList = [];
                    let oTag = null;

                    for (i = 0; i < this.aA.length; i++) {
                        oTag = {};
                        oTag.offsetWidth = this.aA[i].offsetWidth;
                        oTag.offsetHeight = this.aA[i].offsetHeight;
                        this.mcList.push(oTag);
                    }
                    this.sineCosine(0, 0, 0);
                    this.positionAll();
                    this.loadTimer = setInterval(() => {
                        self.update();
                    }, 10);
                }
            },
        },
    }
</script>

<style lang="less">
    #parse-loading{
        position: fixed;
        text-align: center;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 99;

        .modal{
            opacity: 0.5;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-color: #000;
        }

        .tagscloud {
            width: 100%;
            height: 260px;
            position: relative;
            font-size: 12px;
            color: #333;
            text-align: center;
            margin-left: -50px;
            margin-top: 30%;

            a {
                position: absolute;
                top: 0;
                left: 0;
                color: #333;
                text-decoration: none;
                margin: 0 10px 15px 0;
                line-height: 18px;
                text-align: center;
                font-size: 12px;
                padding: 1px 5px;
                display: inline-block;
                border-radius: 3px;
            }

            a.tagc1 {
                background: #666;
                color: #fff;
            }

            a.tagc2 {
                background: #F16E50;
                color: #fff;
            }

            a.tagc5 {
                background: #006633;
                color: #fff;
            }

            a:hover {
                color: #fff;
                background: #0099ff;
            }
        }
    }

</style>