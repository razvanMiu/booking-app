<template>
    <div ref="calendar" id="calendar"></div>
</template>

<script>
    import defaultsDeep from 'lodash.defaultsdeep'
    import '../fullcalendar'
    import $ from 'jquery'
    import 'bootstrap'

    export default {
        data() {
            return {
                overlapSync: false,
            }
        },

        props: {
            events: {
                default() {
                    return []
                },
            },

            eventSources: {
                default() {
                    return []
                },
            },

            editable: {
                default() {
                    return true
                },
            },

            selectable: {
                default() {
                    return true
                },
            },

            selectHelper: {
                default() {
                    return true
                },
            },

            header: {
                default() {
                    return {
                        left:   'prev, next, today',
                        center: 'title',
                        right:  'month, agendaWeek, agendaDay, list'
                    }
                },
            },

            defaultView: {
                default() {
                    return 'month'
                },
            },

            sync: {
                default() {
                    return false
                }
            },

            config: {
                type: Object,
                default() {
                    return {}
                },
            },

            user: Object,

            room: Object,

            newEvent: Object,

            updatedEvent: Object,

            deletedEvent: Object,

            bookings: Array,
        },

        computed: {
            defaultConfig() {
                const self = this
                return {
                    header: this.header,
                    defaultView: this.defaultView,
                    editable: this.editable,
                    selectable: this.selectable,
                    selectHelper: this.selectHelper,
                    aspectRatio: 2,
                    timeFormat: 'HH:mm',
                    events: this.events,
                    eventSources: this.eventSources,

                    eventRender(event, element, view) {
                        if (this.sync) {
                            self.events = cal.fullCalendar('clientEvents'); 
                        }

                        self.$emit('event-render');

                        if(event.isRecurring) {
                           return self.eventInRange(event);
                        }
                    },

                    eventDestroy(event) {
                        if (this.sync) {
                            self.events = cal.fullCalendar('clientEvents')
                        }
                    },

                    eventClick(event, $el) {
                        if(event.status == "finished") {
                            event.finished = true;
                        } else {
                            event.finished = false;
                        }
                        
                        if(event.participants == null) {
                            event.participants = [];
                        }
                        if(event.dow == null) {
                            event.dow = [];
                        }

                        self.$emit('event-modal', event, 'eventClick');
                    },

                    eventDrop(event, delta, revertFunc, jsEvent, ui, view) {
                        if(self.timeSpace(event.start) == 1 && ! event.allDay) {
                            if(event.end == null) {
                                event.end = event.start.clone();
                                event.end.add(30, 'minutes');
                            }

                            event.backgroundColor = '#009688';
                            event.borderColor = '#009688';
                            
                            self.fireMethod('removeEvents', event.id);
                            self.fireMethod('renderEvent', event, true);

                            self.patch(event);
                        } else if(event.allDay) {
                            event.end = event.start.clone();
                            event.end.add(1, 'day');

                            self.patch(event);
                        } else {
                            revertFunc();
                            self.errorAlert(self.INPAST_BOOKING_MESSAGE);
                        }   
                    },

                    eventReceive(...args) {
                        self.$emit('event-receive', ...args)
                    },

                    eventResize(event) {
                        event.backgroundColor = '#009688';
                        event.borderColor = '#009688';

                        self.fireMethod('removeEvents', event.id);
                        self.fireMethod('renderEvent', event, true);

                        self.patch(event);
                    },

                    dayClick(date, jsEvent, view, ...args) {
                        self.$emit('day-click', ...args);
                    },

                    select(start, end, jsEvent, view, resource) {
                        let event;
                        if(view.name != "month") {
                            /* Open event modal */
                            event = {
                                start:              start,
                                end:                end,
                                dow:                [],
                                id:                 '#',
                                user_id:            '',
                                username:           '',
                                title:              '',
                                description:        '',
                                allDay:             false,
                                finished:           false,
                                participants:       [],
                                backgroundColor:    '#009688',
                            };

                            if(self.timeSpace(start) == 1) {
                                self.$emit('event-modal', event, 'select');
                            } else {
                                self.errorAlert(self.INPAST_BOOKING_MESSAGE);
                            }
                        } else {
                            if(self.timeSpace(end) == 1) {
                                self.fireMethod('changeView', 'agendaDay');
                                self.fireMethod('gotoDate', start);
                            } else {
                                self.errorAlert(self.INPAST_BOOKING_MESSAGE);
                            }
                        }
                        self.$emit("select");
                    },

                    selectOverlap(event) {
                        if(event.source.calendar.view.name != "month") {
                            if(self.overlapSync == false && !event.overlap && self.eventInRange(event)) {
                                self.errorAlert(self.OVERLAP_BOOKING_MESSAGE);
                                return event.rendering === 'background';
                            } else {
                                return true;
                            }
                        } else {
                            if(self.timeSpace(event.end) == 0) {
                                self.errorAlert(self.INPAST_BOOKING_MESSAGE);
                            } else {
                                self.fireMethod('changeView', 'agendaDay');
                                self.fireMethod('gotoDate', event.start);
                            }

                            if(! self.overlapSync) {
                                setTimeout(() => {
                                    self.setOverlapSync(false);
                                }, 500); 
                            }
                            
                            self.overlapSync = true;

                            return true;
                        }
                    },
                }
            },
        },

        mounted() {
            const cal = $(this.$el),
                self = this

            this.$on('remove-event', (event) => {
                if(event && event.hasOwnProperty('id')){
                    $(this.$el).fullCalendar('removeEvents', event.id);
                }else{
                    $(this.$el).fullCalendar('removeEvents', event);
                }
            })

            this.$on('rerender-events', () => {
                $(this.$el).fullCalendar('rerenderEvents')
            })

            this.$on('refetch-events', () => {
                $(this.$el).fullCalendar('refetchEvents')
            })

            this.$on('render-event', (event) => {
                $(this.$el).fullCalendar('renderEvent', event)
            })

            this.$on('reload-events', () => {
                $(this.$el).fullCalendar('removeEvents')
                $(this.$el).fullCalendar('addEventSource', this.events)
            })

            this.$on('rebuild-sources', () => {
                $(this.$el).fullCalendar('removeEventSources')
                this.eventSources.map(event => {
                    $(this.$el).fullCalendar('addEventSource', event)
                })
            })

            cal.fullCalendar(defaultsDeep(this.config, this.defaultConfig))
        },

        methods: {
            fireMethod(...options) {
                return $(this.$el).fullCalendar(...options)
            },

            patch(event) {
                axios
                .patch('/book/' + event.id, {
                    event: {
                        'description':  event.description,
                        'title':        event.title.split(' - ')[0],
                        'ranges':       [],
                        'start':        event.start,
                        'dow':          event.dow,
                        'end':          event.end,
                        'status':       'available',
                    },
                })
                .then(response => {
                    this.bookings.forEach((booking, index) => {
                        if(booking.id == event.id) {
                            this.bookings.splice(index, 1);
                            this.bookings.push(event);

                            return true;
                        }
                    })

                    this.successAlert(this.UPDATE_BOOKING_MESSAGE);
                })
                .catch(e => {
                    console.log(e);
                });
            },

            setOverlapSync(val) {
                this.overlapSync = val;
            },
            
            eventInRange(event) {
                if(event.isRecurring) {
                    return event.start.isBefore(event.ranges.end) && 
                        event.end.isAfter(event.ranges.start);
                }

                return true;
            }
        },

        watch: {
            events: {
                deep: true,
                handler(val) {
                    $(this.$el).fullCalendar('removeEvents')
                    $(this.$el).fullCalendar('addEventSource', this.events)
                    $(this.$el).fullCalendar('changeView', 'month');
                },
            },
            eventSources: {
                deep: true,
                handler(val) {
                    this.$emit('rebuild-sources')
                },
            },

            newEvent: {
                deep: true,
                handler(val) {
                    $(this.$el).fullCalendar('renderEvent', val, true);
                    
                    let newBooking = this.iterationCopy(val);
                    if(newBooking.dow.length > 0) {
                        newBooking.start = moment(newBooking.start, "HH:mm");
                        newBooking.end = moment(newBooking.end, "HH:mm");
                    } else {
                        newBooking.start = moment(newBooking.start, "YYYY-MM-DDTHH:mm:ss");
                        newBooking.end = moment(newBooking.end, "YYYY-MM-DDTHH:mm:ss");
                    }

                    this.bookings.push(newBooking);
                },
            },

            updatedEvent: {
                deep: true,
                handler(val) {
                    $(this.$el).fullCalendar('removeEvents', val.id);
                    $(this.$el).fullCalendar('renderEvent', val, true);

                    this.bookings.forEach((booking, index) => {
                        if(booking.id == val.id) {
                            this.bookings.splice(index, 1);

                            
                            let newBooking = this.iterationCopy(val);

                            if(newBooking.dow.length > 0) {
                                newBooking.start = moment(newBooking.start, "HH:mm");
                                newBooking.end = moment(newBooking.end, "HH:mm");
                            } else {
                                newBooking.start = moment(newBooking.start, "YYYY-MM-DDTHH:mm:ss");
                                newBooking.end = moment(newBooking.end, "YYYY-MM-DDTHH:mm:ss");
                            }

                            this.bookings.push(newBooking);

                            return true;
                        }
                    });
                },
            },

            deletedEvent: {
                deep: true,
                handler(val) {
                    $(this.$el).fullCalendar('removeEvents', val.id);

                     this.bookings.forEach((booking, index) => {
                        if(booking.id == val.id) {
                            this.bookings.splice(index, 1);
                            return true;
                        }
                    })
                },
            }
        },

        beforeDestroy() {
            this.$off('remove-event')
            this.$off('rerender-events')
            this.$off('refetch-events')
            this.$off('render-event')
            this.$off('reload-events')
            this.$off('rebuild-sources')
        },
    }
</script>
